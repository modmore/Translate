<?php

/**
 * Class TranslateRestGettranslationsController
 */
class TranslateRestGettranslationsController extends TranslateRestController {
    /**
     * @return string
     */
    public function process() {
        return <<<JSON
[
    {
        "id": 1,
        "namespace": "redactor",
        "topic": "default",
        "language": "nl",
        "key": "redactor.choose_file",
        "original": "Choose File",
        "translation": "",
        "flagged": false,
        "skipped": false,
        "translated": false,
        "order": 1
    },
    {
        "id": 2,
        "namespace": "redactor",
        "topic": "default",
        "language": "nl",
        "key": "redactor.choose_image",
        "original": "Choose Image",
        "translation": "",
        "flagged": false,
        "skipped": false,
        "translated": false,
        "order": 2
    },
    {
        "id": 3,
        "namespace": "redactor",
        "topic": "default",
        "language": "nl",
        "key": "redactor.link",
        "original": "Link",
        "translation": "",
        "flagged": false,
        "skipped": false,
        "translated": false,
        "order": 3
    }
]
JSON;
    }
}
