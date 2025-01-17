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
    
    /**
     * Constructeur de la classe Question.
     * @param string $label Le libellé de la question.
     * @param string $type Le type de la question (checkbox, radio, text).
     * @param string $answer La réponse correcte à la question.
     * @param string|null $uuid L'UUID de la question (généré automatiquement si non fourni).
     */
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

    /**
     * Ajoute un choix à la question.
     * @param string $choice Le choix à ajouter.
     * @throws Exception Si la question n'accepte pas de réponses multiples.
     */
    public function addChoice(string $choice) {
        if ($this->isMultipleAnswers) {
            $this->choices[] = $choice;
        } else {
            throw new Exception("This question does not accept multiple answers");
        }
    }

    /**
     * Retourne l'UUID de la question.
     * @return string L'UUID de la question.
     */
    public function getUuid() {
        return $this->uuid;
    }

    /**
     * Définit l'UUID de la question.
     * @param string $uuid L'UUID de la question.
     */
    public function setUuid(string $uuid) {
        $this->uuid = $uuid;
    }

    /**
     * Retourne le libellé de la question.
     * @return string Le libellé de la question.
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * Définit le libellé de la question.
     * @param string $label Le libellé de la question.
     */
    public function setLabel(string $label) {
        $this->label = $label;
    }

    /**
     * Retourne le type de la question.
     * @return string Le type de la question.
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Définit le type de la question.
     * @param string $type Le type de la question.
     */
    public function setType(string $type) {
        $this->type = $type;
    }

    /**
     * Indique si la question accepte des réponses multiples.
     * @return bool True si la question accepte des réponses multiples, sinon false.
     */
    public function isMultipleAnswers() {
        return $this->isMultipleAnswers;
    }

    /**
     * Définit si la question accepte des réponses multiples.
     * @param bool $isMultipleAnswers True si la question accepte des réponses multiples, sinon false.
     */
    public function setMultipleAnswers(bool $isMultipleAnswers) {
        $this->isMultipleAnswers = $isMultipleAnswers;
    }

    /**
     * Retourne les choix de la question.
     * @return array Les choix de la question.
     */
    public function getChoices() {
        return $this->choices;
    }

    /**
     * Définit les choix de la question.
     * @param array $choices Les choix de la question.
     */
    public function setChoices(array $choices) {
        $this->choices = $choices;
    }

    /**
     * Retourne la réponse correcte à la question.
     * @return string La réponse correcte à la question.
     */
    public function getAnswer() {
        return $this->answer;
    }

    /**
     * Définit la réponse correcte à la question.
     * @param string $answer La réponse correcte à la question.
     */
    public function setAnswer(string $answer) {
        $this->answer = $answer;
    }

    /**
     * Vérifie si une réponse donnée est correcte.
     * @param string $answer La réponse à vérifier.
     * @return bool True si la réponse est correcte, sinon false.
     */
    public function checkAnswer(string $answer) {
        return $this->answer == $answer;
    }

    /**
     * Vérifie si une autre question est égale à celle-ci.
     * @param Question $question La question à comparer.
     * @return bool True si les questions sont égales, sinon false.
     */
    public function equals(Question $question) {
        return $this->uuid == $question->getUuid();
    }

    /**
     * Méthode abstraite pour rendre la question en HTML.
     * @return string Le HTML de la question.
     */
    public abstract function render();
}
?>