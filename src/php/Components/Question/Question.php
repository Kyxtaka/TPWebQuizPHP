<?php
namespace Components\Question;
use Components\Form\TextField;
use Components\Form\Checkbox;
use Components\Form\Radiobox;
use \Exception;

abstract class Question {
    private string $uuid;
    private string $label ;
    private string $type ;
    private bool $isMultipleAnswers ;
    private array $choices ;
    private string  $answer ;
    
    public function __construct(string $label, string $type, string $answer, string $uuid = null) {
        if ($uuid == null) {
            $uuid = bin2hex(random_bytes(32));
        }
        $this->uuid = $uuid;
        $this->label = $label;
        $this->type = $type;
        $this->choices = [];
        if ($type == "checkbox" || $type == "radio") {
            $this->isMultipleAnswers = true;
        } else {
            $this->isMultipleAnswers = false;
            
        }
        $this->answer = $answer;
    } 

    public function addChoice(string $choice) {
        if ($this->isMultipleAnswers) {
            $this->choices[] = $choice;
        } else {
            throw new Exception("This question does not accept multiple answers");
        }
    }

    public function getUuid() {
        return $this->uuid;
    }

    public function setUuid(string $uuid) {
        $this->uuid = $uuid;
    }

    public function getLabel() {
        return $this->label;
    }

    public function getType() {
        return $this->type;
    }

    public function isMultipleAnswers() {
        return $this->isMultipleAnswers;
    }

    public function getChoices() {
        return $this->choices;
    }

    public function getAnswer() {
        return $this->answer;
    }

    public function setAnswer(string $answer) {
        $this->answer = $answer;
    }

    public function setChoices(array $choices) {
        $this->choices = $choices;
    }

    public function setMultipleAnswers(bool $isMultipleAnswers) {
        $this->isMultipleAnswers = $isMultipleAnswers;
    }

    public function setType(string $type) {
        $this->type = $type;
    }

    public function setLabel(string $label) {
        $this->label = $label;
    }

    public function checkAnswer(string $answer) {
        return $this->answer == $answer;
    }

    public function equals(Question $question) {
        return $this->uuid == $question->getUuid();
    }

    public abstract function render(); 
}
?>