<?php
namespace Data;
use Components\Question\Question;
use Components\Question\QuestionCheckBox;
use Components\Question\QuestionRadioBox;
use Components\Question\QuestionTextField;
use Components\Question\Quizz;

class JSONprovider {

    public static string $questionsPath = 'data/json/global/questions.json';
    public static string $quizzPath = 'data/json/global/quizz.json';

    static function loadJSON(string $path): array | null {
        $json = file_get_contents($path);
        // print_r(json_decode(file_get_contents('data/json/global/quizz.json'), true));
        return json_decode($json, true);
    }

    static function saveQuestionToSession($questionArray) {
        if (isset($_SESSION['questions'])) {
            $questions = $_SESSION['questions'];
            foreach ($questionArray as $question) {
                $add = true;
                foreach ($questions as $q) {
                    if ($q->getUuid() == $question->getUuid()) {
                        $add = false;
                        break;
                    } 
                }
                if ($add) {
                    $questions[] = $question;
                }
            }
            $_SESSION['questions'] = $questions;
        }else {
            $_SESSION['questions'] = $questionArray;
        }
    }

    static function saveQuizzToSession($quizzArray) {
        if (!empty($_SESSION['quizzs'])) {
            $quizzs = $_SESSION['quizzs'];
            var_dump($quizzs);
            foreach ($quizzArray as $quizz) {
                $add = true;
                foreach ($quizzs as $q) {
                    if ($q->getUuid() == $quizz->getUuid()) {
                        $add = false;
                        break;
                    }
                }
                if ($add) {
                    $quizzs[] = $quizz;
                }
            }
            $_SESSION['quizzs'] = $quizzs;
        }else {
            $_SESSION['quizzs'] = $quizzArray;
        }
    }

    static function loadQuestions(array $data, bool $save = false): array {
        $questions = [];
        foreach ($data as $questionData) {
            // $question = new Question($questionData['label'], $questionData['type'], $questionData['correct'], $questionData['uuid']);
            switch ($questionData['type']) {
                case "checkbox":
                    $question = new QuestionCheckBox($questionData['label'], $questionData['type'], $questionData['correct'], $questionData['uuid']);
                    break;
                case "radio":
                    $question = new QuestionRadioBox($questionData['label'], $questionData['type'], $questionData['correct'], $questionData['uuid']);
                    break;
                case "text":
                    $question = new QuestionTextField($questionData['label'], $questionData['type'], $questionData['correct'], $questionData['uuid']);
                    break;
                default:
                    break;
            }
            foreach ($questionData['choices'] as $choiceData) {
                $question->addChoice($choiceData);
            }
            $questions[] = $question;
        }
        if ($save) {
            self::saveQuestionToSession($questions);
        }
        return $questions;
    }

    static function loadQuizzs(array $data, bool $save = false): array {
        $quizzArray = [];
        $questionsArray = [];
        $qData = self::loadQuestions(self::loadJSON(self::$questionsPath), true);
        foreach ($data as $quizzData) {
            $quizz = new Quizz($quizzData['uuid'], $quizzData['label']);
            foreach ($quizzData['questions'] as $questionData) {
                if (self::isQuestionUuidExist($questionData['uuid'])) {
                    foreach ($qData as $q) {
                        if ($q->getUuid() == $questionData['uuid']) {
                            $quizz->addQuestion($q);
                            $questionsArray[] = $q;
                            break;
                        }
                    }
                }else {
                    switch ($questionData['type']) {
                        case "checkbox":
                            $question = new QuestionCheckBox($questionData['label'], $questionData['type'], $questionData['correct'], $questionData['uuid']);
                            break;
                        case "radio":
                            $question = new QuestionRadioBox($questionData['label'], $questionData['type'], $questionData['correct'], $questionData['uuid']);
                            break;
                        case "text":
                            $question = new QuestionTextField($questionData['label'], $questionData['type'], $questionData['correct'], $questionData['uuid']);
                            break;
                        default:
                            break;
                    }
        
                    foreach ($questionData['choices'] as $choiceData) {
                        $question->addChoice($choiceData);
                    }
                    $quizz->addQuestion($question);
                    $questionsArray[] = $question;
                }
            }
            $quizzArray[] = $quizz;
        }
        if ($save) {
            self::saveQuizzToSession($quizzArray);  
            self::saveQuestionToSession($questionsArray);
            // self::saveJSON();
        }
        return $quizzArray;
    }

    static function saveQuestionJSON() {
        $questions = $_SESSION['questions'];
        $data = [];
        foreach ($questions as $question) {
            $questionData = [
                'uuid' => $question->getUuid(),
                'label' => $question->getLabel(),
                'type' => $question->getType(),
                'choices' => $question->getChoices(),
                'correct' => $question->getAnswer()                     
            ];
            $data[] = $questionData;
        }
        $json = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents('data/json/global/questions.json', $json);
    }

    static function saveQuizzJSON() {
        $quizzs = $_SESSION['quizzs'];
        $data = [];
        foreach ($quizzs as $quizz) {
            $quizzData = [
                'uuid' => $quizz->getUuid(),
                'label' => $quizz->getLabel(),
                'questions' => []
            ];
            foreach ($quizz->getQuestions() as $question) {
                $questionData = [
                    'uuid' => $question->getUuid(),
                    'label' => $question->getLabel(),
                    'type' => $question->getType(),
                    'choices' => $question->getChoices(),
                    'correct' => $question->getAnswer()
                ];
                $quizzData['questions'][] = $questionData;
            }
            $data[] = $quizzData;
        }
        $json = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents('data/json/global/quizz.json', $json);
    }

    static function saveJSON() {
        self::saveQuestionJSON();
        self::saveQuizzJSON();
    }

    static function isQuestionUuidExist(string $uuid): bool {
        $questions = $_SESSION['questions'];
        foreach ($questions as $question) {
            if ($question->getUuid() == $uuid) {
                return true;
            }
        }
        return false;
    }

    static function clearSession() {
        unset($_SESSION['questions']);
        unset($_SESSION['quizzs']);
    }

    static function clearJSON($questionsPath = 'data/json/global/questions.json', $quizzPath = 'data/json/global/quizz.json') {
        file_put_contents($questionsPath, '');
        file_put_contents($quizzPath, '');
    }
}
?>