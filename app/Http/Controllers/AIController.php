<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'field' => 'required|string|in:subtitle,extra_info',
            'event_type' => 'required|string',
            'title' => 'nullable|string|max:200',
        ]);

        $field = $request->input('field');
        $eventType = $request->input('event_type');
        $title = trim((string) $request->input('title', ''));
        $apiKey = env('GEMINI_API_KEY');

        if (empty($apiKey)) {
            return response()->json([
                'text' => $this->getMockGeneration($field, $eventType),
                'source' => 'mock',
            ]);
        }

        $fieldNames = [
            'subtitle' => 'subtítulo corto y acogedor (máximo 12 palabras)',
            'extra_info' => 'nota especial breve (2 o 3 líneas cortas) que mencione vestimenta y regalos de forma directa, sin adornos ni párrafos largos',
        ];
        $seed = substr(bin2hex(random_bytes(3)), 0, 6);
        $titleLine = $title !== ''
            ? "El título específico del evento es: \"{$title}\". Ajustá el texto para que sea coherente con ese título (usá los nombres, edades o detalles que aparezcan). "
            : '';
        $prompt = "Genera un texto para el campo '" . $fieldNames[$field] . "' de una invitación de tipo '" . $eventType . "'. "
                . $titleLine
                . "Debe sonar único y original, EVITA frases genéricas típicas de plantillas. "
                . "Sé creativo con las palabras, imágenes y ritmo — nunca repitas la misma idea entre variantes (semilla: {$seed}). "
                . "Devuelve exclusivamente el texto generado, sin explicaciones ni comillas, en un tono alegre, tierno y elegante. "
                . "REQUISITO CRÍTICO: No utilices emojis, iconos ni ningún símbolo decorativo visual. Solo texto limpio.";

        try {
            $response = Http::timeout(30)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-lite-latest:generateContent?key=" . $apiKey,
                [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ],
                    'generationConfig' => [
                        'temperature' => 1.1,
                        'topP' => 0.95,
                    ],
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                $generatedText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                if (trim($generatedText) !== '') {
                    return response()->json(['text' => trim($generatedText), 'source' => 'ai']);
                }
            }

            $status = $response->status();
            $errorReason = match (true) {
                $status === 429 => 'cuota_excedida',
                $status === 503 => 'servicio_saturado',
                $status === 401 || $status === 403 => 'key_invalida',
                default => 'error_api_' . $status,
            };
            return response()->json([
                'text' => $this->getMockGeneration($field, $eventType),
                'source' => 'mock',
                'error' => $errorReason,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'text' => $this->getMockGeneration($field, $eventType),
                'source' => 'mock',
                'error' => 'excepcion',
            ]);
        }
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
            'event_id' => 'required|exists:events,id',
        ]);

        $userMessage = $request->input('message');
        $event = Event::where('id', $request->input('event_id'))
            ->where('is_published', true)
            ->firstOrFail();
        $apiKey = env('GEMINI_API_KEY');

        if (empty($apiKey)) {
            return response()->json([
                'reply' => $this->getMockReply($userMessage, $event)
            ]);
        }

        $dateStr = $event->date ? $event->date->translatedFormat('l d \d\e F \a \l\a\s g:i A') : 'Por definir';

        $systemInstruction = "Actúa como un tierno, servicial y alegre asistente virtual para la fiesta del evento: '{$event->title}'. "
                           . "Tu objetivo es responder de forma corta (máximo 2 párrafos), amigable y útil a las dudas de los invitados "
                           . "utilizando la siguiente información del evento:\n"
                           . "- Título: {$event->title}\n"
                           . "- Subtítulo: {$event->subtitle}\n"
                           . "- Fecha y Hora: {$dateStr}\n"
                           . "- Lugar / Dirección: {$event->place}\n"
                           . "- Notas Especiales (Vestimenta, Regalos, etc.): {$event->extra_info}\n\n"
                           . "Si te preguntan algo que no está en la información anterior (por ejemplo, si habrá alcohol, estacionamiento privado, etc.) responde amablemente indicando que no tienes ese detalle y que pueden consultar al organizador. "
                           . "REQUISITO CRÍTICO: No utilices emojis ni iconos gráficos en tus respuestas. Responde directamente al mensaje del invitado.";

        try {
            $response = Http::timeout(30)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-lite-latest:generateContent?key=" . $apiKey,
                [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => "Instrucciones de Contexto:\n" . $systemInstruction . "\n\nMensaje del Invitado:\n" . $userMessage]
                            ]
                        ]
                    ]
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                return response()->json(['reply' => trim($reply)]);
            }
        } catch (\Exception $e) {
            // Silently fall back to mock on exception
        }

        return response()->json([
            'reply' => $this->getMockReply($userMessage, $event)
        ]);
    }

    private function getMockGeneration($field, $eventType)
    {
        $presets = [
            'babyshower' => [
                'subtitle' => 'Un nuevo capítulo de amor y pañales está por comenzar',
                'extra_info' => "Vestimenta: Colores pastel.\nRegalos: Lluvia de sobres.",
            ],
            'cumple' => [
                'subtitle' => '¡Celebrando un año más de risas, aventuras y momentos mágicos!',
                'extra_info' => "Vestimenta: Casual.\nRegalos: Tu presencia, y si querés, un detalle libre.",
            ],
            'bautizo' => [
                'subtitle' => 'Acompáñanos a celebrar el Sacramento del Bautizo',
                'extra_info' => "Vestimenta: Semiformal.\nRegalos: Lluvia de sobres.",
            ],
            'revelacion' => [
                'subtitle' => '¿Rosa o Celeste? ¡Ven a descubrir el gran secreto con nosotros!',
                'extra_info' => "Vestimenta: Rosa si crees niña, celeste si crees niño.\nRegalos: Pañales o ropita neutra.",
            ],
            'bienvenida' => [
                'subtitle' => '¡Hola mundo! Ya estoy aquí y mis papis quieren presentarte',
                'extra_info' => "Vestimenta: Libre.\nRegalos: Pañales o ropita de 3 meses en adelante.",
            ],
            'comunion' => [
                'subtitle' => 'Mi Primera Comunión: Un paso importante en mi fe y camino espiritual',
                'extra_info' => "Vestimenta: Formal.\nRegalos: Muestra de cariño libre.",
            ],
            'boda' => [
                'subtitle' => 'Dos vidas, una historia. Nos casamos y queremos que estés con nosotros.',
                'extra_info' => "Vestimenta: Etiqueta / Formal.\nRegalos: Lluvia de sobres.",
            ],
            'quinceanero' => [
                'subtitle' => '¡Estoy cumpliendo 15! Ven a celebrar esta noche mágica conmigo.',
                'extra_info' => "Vestimenta: Elegante de noche.\nRegalos: Sugerencia libre.",
            ],
            'graduacion' => [
                'subtitle' => 'Terminó una etapa y quiero celebrarlo contigo. ¡Me gradué!',
                'extra_info' => "Vestimenta: Semiformal.\nRegalos: A tu gusto.",
            ],
            'aniversario' => [
                'subtitle' => 'Un año más juntos vale una celebración. Acompáñanos a brindar.',
                'extra_info' => "Vestimenta: Elegante.\nRegalos: Tu compañía es lo más importante.",
            ],
            'despedida' => [
                'subtitle' => '¡Última noche de soltería! Ven a despedir esta etapa conmigo.',
                'extra_info' => "Vestimenta: Divertida / Temática.\nRegalos: Sorpresas bienvenidas.",
            ],
            'general' => [
                'subtitle' => 'Te invito a compartir un momento especial conmigo.',
                'extra_info' => "Vestimenta: Casual.\nRegalos: A tu criterio.",
            ],
        ];

        return $presets[$eventType][$field] ?? '¡Te esperamos en nuestro gran día!';
    }

    private function getMockReply($message, $event)
    {
        $message = mb_strtolower($message, 'UTF-8');
        $dateStr = $event->date ? $event->date->translatedFormat('l d \d\e F \a \l\a\s g:i A') : 'Por confirmar';
        
        if (str_contains($message, 'donde') || str_contains($message, 'lugar') || str_contains($message, 'direccion') || str_contains($message, 'dirección') || str_contains($message, 'ubicacion') || str_contains($message, 'ubicación') || str_contains($message, 'como llego') || str_contains($message, 'cómo llego') || str_contains($message, 'llegar')) {
            return "El evento se llevará a cabo en: " . $event->place . ". ¡Te esperamos!";
        }
        
        if (str_contains($message, 'cuando') || str_contains($message, 'cuándo') || str_contains($message, 'fecha') || str_contains($message, 'hora') || str_contains($message, 'dia') || str_contains($message, 'día')) {
            return "La cita es el " . $dateStr . ". ¡No faltes!";
        }
        
        if (str_contains($message, 'regalo') || str_contains($message, 'sobres') || str_contains($message, 'llevar') || str_contains($message, 'obsequio')) {
            if ($event->extra_info) {
                return "En las notas especiales dice:\n" . $event->extra_info;
            }
            return "¡Tu presencia es nuestro mejor regalo! Si deseas tener un detalle, cualquier muestra de cariño será muy bien recibida.";
        }
 
        if (str_contains($message, 'vestir') || str_contains($message, 'ropa') || str_contains($message, 'vestimenta') || str_contains($message, 'outfit') || str_contains($message, 'dress code') || str_contains($message, 'traje')) {
            if ($event->extra_info) {
                return "Sobre la vestimenta, el organizador indica:\n" . $event->extra_info;
            }
            return "No hay un código de vestimenta estricto. ¡Ven cómodo/a para celebrar y pasar un gran momento!";
        }
 
        return "¡Hola! Soy el asistente virtual del evento. Pregúntame sobre la fecha, la ubicación, la vestimenta o sugerencias de regalos.";
    }
}
