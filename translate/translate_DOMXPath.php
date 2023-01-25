<?php
/*
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require 'vendor/autoload.php';

use Google\Cloud\Translate\V3\TranslateTextGlossaryConfig;
use Google\Cloud\Translate\V3\TranslationServiceClient;


// [START v3_translate_as_text_with_glossary]

/**
 * @param string $text          The text to translate.
 * @param string $targetLanguage    Language to translate to.
 * @param string $sourceLanguage    Language of the source.
 * @param string $projectId     Your Google Cloud project ID.
 * @param string $glossaryId    Your glossary ID.
 */
function v3_translate_as_text_with_glossary(
    string $text,
    string $targetLanguage,
    string $sourceLanguage,
    string $projectId,
    string $glossaryId
) {
    $translationServiceClient = new TranslationServiceClient();

    $glossaryPath = $translationServiceClient->glossaryName(
        $projectId,
        'us-central1',
        $glossaryId
    );
    $contents = [$text];
    $formattedParent = $translationServiceClient->locationName(
        $projectId,
        'us-central1'
    );
    $glossaryConfig = new TranslateTextGlossaryConfig();
    $glossaryConfig->setGlossary($glossaryPath);
    $glossaryConfig->setIgnoreCase(true);

    // Optional. Can be "text/plain" or "text/html".
    $mimeType = 'text/plain';

    try {
        $response = $translationServiceClient->translateText(
            $contents,
            $targetLanguage,
            $formattedParent,
            [
                'sourceLanguageCode' => $sourceLanguage,
                'glossaryConfig' => $glossaryConfig,
                'mimeType' => $mimeType
            ]
        );
        // Display the translation for each input text provided
        foreach ($response->getGlossaryTranslations() as $translation) {
	    printf("Running function: %s\n", __FUNCTION__);
	    printf("Input HTML: %s\n",$text);
            printf("Translated text: %s\n" . PHP_EOL, $translation->getTranslatedText());
            return $translation->getTranslatedText();
        }
    } finally {
        $translationServiceClient->close();
    }
}
// [END v3_translate_as_text_with_glossary]


// [START v3_translate_as_text_with_glossary_DOMXPath]
/**
 * @param string $text          The text to translate.
 * @param string $targetLanguage    Language to translate to.
 * @param string $sourceLanguage    Language of the source.
 * @param string $projectId     Your Google Cloud project ID.
 * @param string $glossaryId    Your glossary ID.
 */
 function v3_translate_as_text_with_glossary_DOMXPath(
    string $text,
    string $targetLanguage,
    string $sourceLanguage,
    string $projectId,
    string $glossaryId
) {
    printf("Running function: %s\n", __FUNCTION__);
    printf("Input HTML: %s\n",$text);
    
    libxml_use_internal_errors(true);
$dom = new DomDocument();
$dom->loadHTML($text, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
#$dom->loadHTMLFile("html_form_in.html");
$xpath = new DOMXPath($dom);

foreach ($xpath->query('//text()') as $htmltext) {
    if (trim($htmltext->nodeValue)) {
        $htmltext->nodeValue = v3_translate_as_text_with_glossary($htmltext->nodeValue,$targetLanguage,$sourceLanguage,$projectId,$glossaryId);
    }
}
     
#echo $dom->saveHTML();
printf("Output HTML: %s\n",$dom->saveHTML());
#$dom->saveHTMLFile("html_form_out.html");
}

// Call the function

v3_translate_as_text_with_glossary_DOMXPath($argv[1],$argv[2],$argv[3],$argv[4],$argv[5]);