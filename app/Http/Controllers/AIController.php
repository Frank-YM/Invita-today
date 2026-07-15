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
        ]);

        $field = $request->input('field');
        $eventType = $request->input('event_type');
        $apiKey = env('GEMINI_API_KEY');

        if (empty($apiKey)) {
            return response()->json([
                'text' => $this->getMockGeneration($field, $eventType)
            ]);
        }

        $fieldNames = [
            'subtitle' => 'subtítulo corto y acogedor',
            'extra_info' => 'sección de notas especiales (con detalles creativos sobre código de vestimenta, sugerencia de regalos y notas adicionales)'
        ];
        $prompt = "Genera un texto para el campo '" . $fieldNames[$field] . "' de una invitación de tipo '" . $eventType . "'. "
                . "Devuelve exclusivamente el texto generado, sin explicaciones ni comillas, en un tono alegre, tierno y elegante. "
                . "REQUISITO CRÍTICO: No utilices emojis, iconos ni ningún símbolo decorativo visual (por ejemplo, nada de 🕊️, 🎁, ✨, 🌸, 🎂, etc.). Solo texto limpio.";

        try {
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $generatedText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                return response()->json(['text' => trim($generatedText)]);
            }
        } catch (\Exception $e) {
            // Silently fall back to mock on API exception
        }

        return response()->json([
            'text' => $this->getMockGeneration($field, $eventType)
        ]);
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
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey, [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => "Instrucciones de Contexto:\n" . $systemInstruction . "\n\nMensaje del Invitado:\n" . $userMessage]
                        ]
                    ]
                ]
            ]);

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
                'extra_info' => "Vestimenta: Colores pastel.\nSugerencia de regalo: Lluvia de sobres.\nNotas: Tendremos una hermosa mesa de postres y divertidos juegos para celebrar.",
            ],
            'cumple' => [
                'subtitle' => '¡Celebrando un año más de risas, aventuras y momentos mágicos!',
                'extra_info' => "Vestimenta: Casual.\nRegalos: ¡Tu presencia! Pero si quieres tener un detalle, un obsequio libre será fantástico.\nNotas: Ven con hambre de pastel.",
            ],
            'bautizo' => [
                'subtitle' => 'Acompáñanos a celebrar el Sacramento del Bautizo',
                'extra_info' => "Vestimenta: Semiformal.\nDetalle: Lluvia de sobres.\nNotas: Después de la ceremonia religiosa, compartiremos un almuerzo familiar.",
            ],
            'revelacion' => [
                'subtitle' => '¿Rosa o Celeste? ¡Ven a descubrir el gran secreto con nosotros!',
                'extra_info' => "Vestimenta: Ven vestido de Rosa si crees que es niña, o de Celeste si crees que es niño.\nRegalos: Pañales o ropita neutra.\nDetalles: La revelación se hará a las 5:00 PM.",
            ],
            'bienvenida' => [
                'subtitle' => '¡Hola mundo! Ya estoy aquí y mis papis quieren presentarte',
                'extra_info' => "Medida especial: Agradecemos evitar visitarme si tienes algún malestar.\nRegalos: Ropa de 3 meses en adelante o pañales.\nNotas: Ven a darme amor.",
            ],
            'comunion' => [
                'subtitle' => 'Mi Primera Comunión: Un paso importante en mi fe y camino espiritual',
                'extra_info' => "Vestimenta: Formal / Respetuosa.\nSugerencia: Muestra de cariño libre.\nNotas: Agradecemos confirmar tu asistencia para organizar la recepción.",
            ]
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
