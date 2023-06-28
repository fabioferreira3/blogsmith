<?php

namespace App\Helpers;

use App\Enums\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AudioHelper
{
    public static function getVoicesByLanguage(Language $language): Collection
    {
        switch ($language) {
            case Language::ENGLISH:
                return collect([
                    ['id' => Str::uuid(), 'value' => 'Joanna', 'label' => 'Maura, Female', 'url' => self::getAudioUrl('voices/voice.eb8e8334-b965-443b-81e8-59799c9bc604.mp3')],
                    ['id' => Str::uuid(), 'value' => 'Salli', 'label' => 'Clara, Female', 'url' => self::getAudioUrl('voices/voice.95b0d654-8822-4065-9239-d1bb1ee5fe0a.mp3')],
                    ['id' => Str::uuid(), 'value' => 'Kimberly', 'label' => 'Jenny, Female', 'url' => self::getAudioUrl('voices/voice.59314af7-7d9c-44e7-acec-b533c8c988ba.mp3')],
                    ['id' => Str::uuid(), 'value' => 'Kendra', 'label' => 'Sandra, Female', 'url' => self::getAudioUrl('voices/voice.d6d2d245-303f-4b7d-9b58-ab5d2c256082.mp3')],
                    ['id' => Str::uuid(), 'value' => 'Ivy', 'label' => 'Kera, Kid female', 'url' => self::getAudioUrl('voices/voice.596af08d-aff7-46eb-8b95-c92a349b98e1.mp3')],
                    ['id' => Str::uuid(), 'value' => 'Ruth', 'label' => 'Megan, Female', 'url' => self::getAudioUrl('voices/voice.3a40611d-c3d6-4890-b591-ee8c488354ab.mp3')],
                    ['id' => Str::uuid(), 'value' => 'Kevin', 'label' => 'John, Kid male', 'url' => self::getAudioUrl('voices/voice.88822cca-a495-4f35-8734-ea10d5e5918f.mp3')],
                    ['id' => Str::uuid(), 'value' => 'Matthew', 'label' => 'Luke, Male', 'url' => self::getAudioUrl('voices/voice.f784842d-8b87-49d0-9d2c-9eafa6b16106.mp3')],
                    ['id' => Str::uuid(), 'value' => 'Justin', 'label' => 'Tom, Kid male', 'url' => self::getAudioUrl('voices/voice.adb68b51-1583-46a9-83cc-9f86d7e5fe2c.mp3')],
                    ['id' => Str::uuid(), 'value' => 'Joey', 'label' => 'Carl, Male', 'url' => self::getAudioUrl('voices/voice.65d34168-06da-40c6-bf46-d83ed672c183.mp3')],
                    ['id' => Str::uuid(), 'value' => 'Stephen', 'label' => 'Paul, Male', 'url' => self::getAudioUrl('voices/voice.bfd855d7-042d-4913-8938-ada5b8efe2f3.mp3')],
                ]);
                break;
            case Language::PORTUGUESE:
                return collect([
                    ['id' => Str::uuid(), 'value' => 'Vit\'oria', 'label' => 'Marcela, Feminino', 'url' => self::getAudioUrl('voices/voice-pt.ab4bfbef-42f4-4cf3-963b-91b9b330ce1c.mp3')],
                    ['id' => Str::uuid(), 'value' => 'Camila', 'label' => 'Paola, Feminino', 'url' => self::getAudioUrl('voices/voice-pt.c92fb7a1-87e3-453a-8072-c2575efd02fb.mp3')],
                    ['id' => Str::uuid(), 'value' => 'Thiago', 'label' => 'Carlos, Masculino', 'url' => self::getAudioUrl('voices/voice-pt.211525bf-a9bd-435e-a508-d263023c85b0.mp3')],
                ]);
                break;
            case Language::SPANISH:
                return collect([
                    ['id' => Str::uuid(), 'value' => 'Lucia', 'label' => 'Lucia, Femenino', 'url' => self::getAudioUrl('voices/voice-es.20452ee0-9edb-4f89-942a-a9721a4f10f3.mp3')],
                    ['id' => Str::uuid(), 'value' => 'Lupe', 'label' => 'Lupe, Femenino', 'url' => self::getAudioUrl('voices/voice-es.95920eb1-8105-43b2-9e51-8f102e3d2a53.mp3')],
                    ['id' => Str::uuid(), 'value' => 'Mia', 'label' => 'Mia, Femenino', 'url' => self::getAudioUrl('voices/voice-es.db96a087-da21-4c17-ab04-ee795e7a1289.mp3')],
                    ['id' => Str::uuid(), 'value' => 'Sergio', 'label' => 'Sergio, Masculino', 'url' => self::getAudioUrl('voices/voice-es.9f90e97b-80f0-46dc-9693-a7867a31c97c.mp3')],
                    ['id' => Str::uuid(), 'value' => 'Pedro', 'label' => 'Pedro, Masculino', 'url' => self::getAudioUrl('voices/voice-es.dde9d6bf-8d97-4c96-91ec-de3d17c09fbd.mp3')],
                ]);
                break;
            default:
                return collect([]);
        }
    }

    public static function getAudioUrl($fileName)
    {
        return Storage::temporaryUrl($fileName, now()->addMinutes(30));
    }
}
