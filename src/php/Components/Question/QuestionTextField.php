<?php
namespace Components\Question;
use Components\Question\Question;
use Components\Form\TextField;
use Components\Form\Checkbox;
use Components\Form\Radiobox;
use \Exception;

class QuestionTextField extends Question{    

    /**
     * Constructeur de la classe QuestionTextField
     * @param string $uuid L'identifiant unique de la question.
     * @param string $label Le label de la question.
     */
    public function render() {
        $html = '<div class="question">';
        $input  = new TextField($this->getUuid(), $this->getLabel() . " :");
        $html .= $input->render();
        $html .= '</div>';
        return $html;
    }
}
?>