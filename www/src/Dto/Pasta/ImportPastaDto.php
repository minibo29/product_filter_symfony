<?php

namespace App\Dto\Pasta;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializing;

class ImportPastaDto
{
    /**
     * @Serializing\Type("integer")
     * @var $id integer
     */
    public $id;

    /**
     * @Assert\NotBlank
     * @Serializing\Type("sring")
     * @var $name string
     */
    public $name;

    /**
     * @Serializing\Type("sring")
     * @var $region string
     */
    public $region;

    /**
     * @Assert\NotBlank
     * @Serializing\Type("float")
     * @var $price float
     */
    public $price;

    /**
     * @Serializing\Type("sring")
     * @var $productCode string
     */
    public $basePrice;

    /**
     * @Serializing\Type("sring")
     * @var $manufacturer string
     */
    public $manufacturer;

    /**
     * @Serializing\Type("sring")
     * @var $properties string
     */
    public $properties;
}