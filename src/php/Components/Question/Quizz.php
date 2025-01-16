<?php
namespace Components\Question;
use Components\Question\Question;

class Quizz {
    private string $uuid;
    private string $label;
    private array $questions;

    public function __construct(string $uuid = null, string $label,  array $questions = []) {
        if ($uuid == null) {
            $uuid = bin2hex(random_bytes(32));
        }
        $this->uuid = $uuid;
        $this->label = $label;
        $this->questions = $questions;
    }

    public function addQuestion(Question $question) {
        $this->questions[] = $question;
    }

    public function getQuestions() {
        return $this->questions;
    }

    public function getLabel() {
        return $this->label;
    }

    public function getUuid() {
        return $this->uuid; 
    }

    public function render($action, $method = "POST"): string {
        $html = '<h2>Quizz: '. $this->label . '</h2>';
        $html .= '<form action=' . $action . ' method="' . $method . '">';
        $html .= '<input type="hidden" name="quizz" value="' . $this->uuid . '">';
        $inc = 0;
        foreach ($this->questions as $question) {
            $html .= '<div>';
            $html .= $question->render();
            $html .= '</div>';
        }
        $html .= '<button type="submit">Submit</button>';
        $html .= '</form>';
        return $html;
    }
}
