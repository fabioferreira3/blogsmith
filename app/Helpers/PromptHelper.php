<?php

namespace App\Helpers;

use App\Enums\Tone;

class PromptHelper
{
    protected string $language;

    public function __construct($language = 'en')
    {
        $this->language = $language;
    }

    public function summarize($text)
    {
        switch ($this->language) {
            case 'en':
                return "Summarize the following text using maximum of 1500 words: \n\n" . $text;
            case 'pt':
                return "Resuma o seguinte texto usando no máximo 1500 palavras: \n\n" . $text;
            default:
                return '';
        }
    }

    public function rewriteWithSimilarWords($text)
    {
        switch ($this->language) {
            case 'en':
                return "Rewrite the following text using similar words:\n\n" . $text;
            case 'pt':
                return "Reescreva o seguinte texto usando palavras similares: \n\n" . $text;
            default:
                return '';
        }
    }

    public function writeTitle($context, $tone = 'casual', $keyword = null)
    {
        $tone = Tone::fromLanguage($tone, $this->language);
        switch ($this->language) {
            case 'en':
                $withKeyword = $keyword ? ", using the keyword $keyword" : '';
                return "Write a title, with a maximum of 7 words, $withKeyword and with a $tone tone, for the following text: \n\n" . $context . "\n\n\nExamples of good and bad outputs:\n\nBad output:\nTitle: This is the title\n\nGood output:\nThis is the title";
            case 'pt':
                $withKeyword = $keyword ? ", usando a palavra-chave $keyword" : '';
                return "Escreva um título, com no máximo 7 palavras, $withKeyword e com um tom $tone, baseado no seguinte texto: \n\n" . $context . "\n\n\nExemplos de outputs bons e ruins:\n\nOutput ruim:\nTítulo: Este é o título\n\nOutput correto:\nEste é o título";
            default:
                return '';
        }
    }

    public function writeOutline($context, $maxSubtopics, $tone = 'casual')
    {
        $tone = Tone::fromLanguage($tone, $this->language);
        switch ($this->language) {
            case 'en':
                return "Create an indept and comprehensive blog post outline, with maximum of two levels, with a " . $tone . " tone, using roman numerals indicating main topics and alphabet letters to indicate subtopics. The outline must have only $maxSubtopics topics. Do not nest a third level of topics. Do not add inner topics inside the subtopics indicated by alphabet letters, for example: \n\nGood output:\nI. Main Topic \n A. Subtopic 1 \n B. Subtopic 2 \n C. Subtopic 3 \n\nBad output:\nI. Main Topic \nA. Subtopic 1 \nB. Subtopic 2\n   1. Inner topic 1\n   2. Inner topic 2\nC. Subtopic 3\n\n\n The outline should be based on the following text: \n\n" . $context;
            case 'pt':
                return "Crie um esboço de postagem de blog aprofundado e abrangente, com no máximo dois níveis, com um tom " . $tone . ", usando números romanos para indicar os tópicos principais e letras do alfabeto comum para indicar subtópicos. O esboço deve ter somente $maxSubtopics tópicos. Não adicione um terceiro nível de tópicos. Não adicione tópicos internos dentro dos subtópicos indicados com letras do alfabeto comum, por exemplo: \n\nOutput correto:\nI. Tópico Principal \n A. Sub-tópico 1 \n B. Sub-tópico 2 \n C. Sub-tópico 3 \n\nOutput errado:\nI. Tópico Principal \nA. Sub-tópico 1 \nB. Sub-tópico 2\n   1. Sub-tópico interno 1\n   2. Sub-tópico interno 2\nC. Sub-tópico 3\n\n\n O esboço deve ser baseado no seguinte texto: \n\n" . $context;
            default:
                return '';
        }
    }


    public function givenFollowingText($text)
    {
        switch ($this->language) {
            case 'en':
                return "Given the following text: \n\n" . $text . "\n\n\n";
            case 'pt':
                return "Tendo como base o seguinte texto: \n\n" . $text . "\n\n\n";
            default:
                return '';
        }
    }

    public function andGivenFollowingContext($text)
    {
        switch ($this->language) {
            case 'en':
                return "And given the following context:\n\n" . preg_replace('/\s+/', ' ', $text) . "\n\n\n";
            case 'pt':
                return "E levando em conta o seguinte contexto:\n\n" . preg_replace('/\s+/', ' ', $text) . "\n\n\n";
            default:
                return '';
        }
    }

    public function expandOn($text, $tone = 'casual')
    {
        $tone = Tone::fromLanguage($tone, $this->language);
        switch ($this->language) {
            case 'en':
                return "Using a " . $tone . " tone, and using <h3> tags for subtopics and <p> tags for paragraphs, expand on: \n\n" . $text . "\n\n\nFurther instructions:\nWhen expanding the text, do not create new <h3> inner topics. Instead, increase the number of paragraphs.";
            case 'pt':
                return "Usando um tom " . $tone . ", e usando tags <h3> para os sub-tópicos e tags <p> para os parágrafos, expanda: \n\n" . $text . "\n\n\nOutras instruções:\nAo expandir o texto, não crie novos tópicos internos <h3>. Ao invés disso, aumente o número de parágrafos.";
            default:
                return '';
        }
    }

    public function writeMetaDescription($text, $tone = 'casual', $keyword = null)
    {
        $tone = Tone::fromLanguage($tone, $this->language);
        switch ($this->language) {
            case 'en':
                $withKeyword = $keyword ? "using the keyword $keyword," : "";
                return "Write a meta description of a maximum of 20 words, with a $tone tone, $withKeyword for the following text: \n\n" . $text;
            case 'pt':
                $withKeyword = $keyword ? "usando a palavra-chave $keyword," : "";
                return "Escreva uma meta description com no máximo 20 palavras, com um tom $tone, $withKeyword para o seguinte texto: \n\n" . $text;
            default:
                return '';
        }
    }

    public function writeConclusion($text, $tone = 'casual')
    {
        $tone = Tone::fromLanguage($tone, $this->language);
        switch ($this->language) {
            case 'en':
                return "Using a " . $tone . " tone, and using a <p> tag, write a concluding paragraph for the following text: \n\n" . $text;
            case 'pt':
                return "Usando um tom " . $tone . ", e usando tag <p>, escreva um paragrafo de conclusão para o seguinte texto: \n\n" . $text;
            default:
                return '';
        }
    }

    public function setLanguage(string $language)
    {
        $this->language = $language;
    }
}