<?php
namespace Components\Form;

class TextField {
    private $name;
    private $label;

    /**
     * Constructeur
     *
     * @param string $name Le nom de l'élément checkbox (attribut HTML `name`)
     * @param string $label Le texte affiché à côté de la checkbox
     */
    public function __construct(string $name, string $label) {  
        $this->name = $name;
        $this->label = $label;
    }

    /**
     * Génère le HTML de la zone de texte
     *
     * @return string Le code HTML de la zone de texte
     */ 
    public function render(): string {
        return sprintf(
            '<label for="%s">%s</label><input type="text" name="%s" id="%s">', 
            htmlspecialchars($this->name), 
            htmlspecialchars($this->label), 
            htmlspecialchars($this->name),
            htmlspecialchars($this->name)
        );
    }

    /**
     * Obtenir le nom du champ
     *
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Obtenir le texte du label
     *
     * @return string
     */
    public function getLabel(): string {
        return $this->label;
    }
}
?>