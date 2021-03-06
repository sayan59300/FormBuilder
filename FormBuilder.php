<?php

namespace Itval\core\Classes;

/**
 * Class FormBuilder Classe qui génère les formulaires
 * CSS bootsrap 4
 * 
 * Requirement : Itval\core\Classes\Session
 *
 * @package Itval\core\Classes
 * @author  Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
 */
class FormBuilder extends Html
{

    /**
     * Nom du formulaire
     *
     * @var string
     */
    public $name;

    /**
     * Méthode utilisée par le formulaire
     *
     * @var string
     */
    public $method;

    /**
     * Action à la soumission du formulaire
     *
     * @var string
     */
    public $action;

    /**
     * Tableau des éléments du formulaire
     *
     * @var array
     */
    public $elements = [];

    /**
     * FormBuilder constructor.
     *
     * @param $name
     * @param $method
     * @param $action
     */
    public function __construct($name, $method, $action)
    {
        $this->name = $name;
        if ($method === 'put') {
            $this->method = 'post';
            $this->setMethodInput('PUT');
        } elseif ($method === 'patch') {
            $this->method = 'post';
            $this->setMethodInput('PATCH');
        } elseif ($method === 'delete') {
            $this->method = 'post';
            $this->setMethodInput('DELETE');
        } else {
            $this->method = $method;
        }
        $this->action = $action;
    }

    /**
     * Génère un input pour la gestion des failles CSRF et l'ajoute au tableau des elements
     *
     * @param  string $csrfToken
     * @return FormBuilder
     */
    public function setCsrfInput(string $csrfToken): self
    {
        $csrf = '<div>'
            . '<input type="hidden" name="csrf_token" value="' . $csrfToken . '"/>'
            . '</div>';
        $this->elements['token_csrf'] = $csrf;
        return $this;
    }

    /**
     * Génère un input et l'ajoute au tableau des elements
     *
     * @param  string      $type
     * @param  string      $name
     * @param  array       $attributesArgs
     * @param  string|null $textLabel
     * @return FormBuilder
     */
    public function setInput(string $type, string $name, array $attributesArgs = [], string $textLabel = null): self
    {
        $label = self::setLabel($name, $textLabel);
        $classes = $this->getClassAttributes($attributesArgs);
        if (isset($attributesArgs['class'])) {
            unset($attributesArgs['class']);
        }
        if (Session::read('validator_error_' . $name)) {
            $errorClass = ' is-invalid';
        } else {
            $errorClass = '';
        }
        $attributes = self::formatAttributes($attributesArgs);
        if ($type === 'file') {
            $input = '<div class="form-group">' . $label . '<input type="file" '
                . 'class="input-file' . $errorClass . ' ' . $classes . '" name="' . $name . '" ' . 'id="' . $name . '" ' . $attributes . '/>'
                . '<div class="invalid-feedback">' . Session::read('validator_error_' . $name) . '</div></div>';
        } elseif ($type === 'hidden') {
            $input = '<input type="' . $type . '" name="' . $name . '" ' . $attributes . '/>';
        } else {
            $input = '<div class="form-group">' . $label . '<input type="' . $type . '" '
                . 'class="form-control' . $errorClass . ' ' . $classes . '" name="' . $name . '" ' . 'id="' . $name . '" ' . $attributes . '/>'
                . '<div class="invalid-feedback">' . Session::read('validator_error_' . $name) . '</div></div>';
        }
        $this->elements[$name] = $input;
        return $this;
    }

    /**
     * Génère un bouton et l'ajoute au tableau des elements
     *
     * @param  string $type
     * @param  string $name
     * @param  string $texte
     * @param  array  $attributesArgs
     * @return FormBuilder
     */
    public function setButton(string $type, string $name, string $texte, array $attributesArgs = []): self
    {
        $classes = $this->getClassAttributes($attributesArgs, 'btn btn-primary');
        if (isset($attributesArgs['class'])) {
            unset($attributesArgs['class']);
        }
        $attributes = self::formatAttributes($attributesArgs);
        $button = '<div class="form-group"><button type="' . $type . '" '
            . 'class="' . $classes . '" name="' .
            $name . '" ' . $attributes . '>' . $texte . '</button>' . '</div>';
        $this->elements[$name] = $button;
        return $this;
    }

    /**
     * Génère un textArea et l'ajoute au tableau des elements
     *
     * @param  string      $rows
     * @param  string      $name
     * @param  array       $attributesArgs
     * @param  string|null $textLabel
     * @param  string|null $content
     * @return FormBuilder
     */
    public function setTextArea(string $rows, string $name, array $attributesArgs = [], string $textLabel = null, string $content = null): self
    {
        $label = self::setLabel($name, $textLabel);
        $classes = $this->getClassAttributes($attributesArgs);
        if (isset($attributesArgs['class'])) {
            unset($attributesArgs['class']);
        }
        if (Session::read('validator_error_' . $name)) {
            $errorClass = ' is-invalid';
        } else {
            $errorClass = '';
        }
        $attributes = self::formatAttributes($attributesArgs);
        $textArea = '<div class="form-group">' . $label . '<textarea rows="' . $rows . '" '
            . 'class="form-control' . $errorClass . ' ' . $classes . '" name="' . $name . '" ' . $attributes . '>' . $content . '</textarea>'
            . '<div class="invalid-feedback">' . Session::read('validator_error_' . $name) . '</div></div>';
        $this->elements[$name] = $textArea;
        return $this;
    }

    /**
     * Génère un label
     *
     * @param  string      $id
     * @param  string|null $texte
     * @return string
     */
    private static function setLabel(string $id, string $texte = null): string
    {
        if (!$texte) {
            return '<label for="' . $id . '" class="control-label">' . ucfirst($id) . '</label>';
        }
        return '<label for="' . $id . '" class="control-label">' . $texte . '</label>';
    }

    /**
     * Retourne les attributs de la balise html correctement formattés
     *
     * @param  array $attributesArgs
     * @return string
     */
    private static function formatAttributes(array $attributesArgs): string
    {
        if (isset($attributesArgs)) {
            $attributes = self::getAttributes($attributesArgs);
        }
        return $attributes;
    }

    /**
     * Retourne l'attribut class correctement formatté
     *
     * @param  array       $attributes
     * @param  string|null $defaut
     * @return string
     */
    private function getClassAttributes(array $attributes, string $defaut = null): string
    {
        return $classes = $attributes['class'] ?? $defaut ?? '';
    }

    /**
     * Ajoute l'input avec la méthode demandée (PUT, PATCH, DELETE)
     *
     * @param string $method
     */
    private function setMethodInput(string $method): void
    {
        $this->elements['_method'] = '<input type="hidden" name="_METHOD" value="' . $method . '"/>';
    }
}
