<?php
namespace Components\Question;
use Components\Form\TextField;
use Components\Form\Checkbox;
use Components\Form\Radiobox;
use \Exception;

class QuestionRadioBox extends Question {

    /**
     * Constructeur de la classe QuestionRadioBox
     * @param string $uuid L'identifiant unique de la question.
     * @param string $label Le label de la question.
     */
    public function render() {
        $html = '<div class="question">';
        $html .= '<label>' . $this->label . '</label>';
        foreach ($this->getChoices() as $choice) {
            $input  = new Radiobox($this->getUuid(), $choice);
            $html .= $input->render();
        }
        $html .= '</div>';
        return $html;
    }
}
?>