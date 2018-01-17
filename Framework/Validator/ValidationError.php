<?php

namespace Framework\Validator;

class ValidationError
{
    /**
     * @var array
     */
    private $messages = [
        'required'      => 'Le champs %s est requis',
        'empty'         => 'Le champs %s ne peut être vide',
        'slug'          => 'Le champs %s n\'est pas un slug valide',
        'minLength'     => 'Le champs %s doit contenir plus de %d caractères',
        'maxLength'     => 'Le champs %s doit contenir moins de %d caractères',
        'betweenLength' => 'Le champs %s doit contenir entre %d et %d caractères',
        'datetime'      => 'Le champs %s doit être une date valide (%s)',
        'exists'        => 'Le champs %s n\'existe pas sur dans la table %s',
        'unique'        => 'Le champs %s doit être unique',
        'filetype'      => 'Le champs %s n\'est pas au format valide (%s)',
        'uploaded'      => 'Vous devez uploader un fichier',
        'email'         => 'Vous devez renseigner une adresse Email Valide',
        'confirm'       => 'La confirmation du champ champ %s est érronée',
        'numeric'       => 'Le champ %s doit être un nombre valide'
    ];
    /**
     * @var string
     */
    private $key;
    /**
     * @var string
     */
    private $rule;
    /**
     * @var array
     */
    private $attributes;
    
    /**
     * ValidationError constructor.
     * @param string $key
     * @param string $rule
     * @param array  $attributes
     */
    public function __construct(string $key, string $rule, array $attributes = [])
    {
        $this->key        = $key;
        $this->rule       = $rule;
        $this->attributes = $attributes;
    }
    
    /**
     * @return string
     */
    public function __toString()
    {
        if (!array_key_exists($this->rule, $this->messages)) {
            return "Le champs {$this->key} ne correspond pas à la règle {$this->rule}";
        }
        $params = array_merge([$this->messages[$this->rule], $this->key], $this->attributes);
        return (string)sprintf(...$params);
    }
}
