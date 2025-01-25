<?php

namespace App\Utils;

use ReflectionClass;
use ReflectionProperty;

class DtoMapper
{
    /**
     * Mapear un stdClass a una clase DTO.
     *
     * @param object $source El objeto fuente (stdClass).
     * @param string $targetClass El nombre completo de la clase destino.
     * @return object Instancia de la clase destino con los datos mapeados.
     */
    public static function mapToDto(object $source, string $targetClass): object
    {
        if (!class_exists($targetClass)) {
            throw new \InvalidArgumentException("La clase destino $targetClass no existe.");
        }

        $reflectionClass = new ReflectionClass($targetClass);

        // Obtener los argumentos requeridos del constructor
        $constructor = $reflectionClass->getConstructor();
        $parameters = $constructor ? $constructor->getParameters() : [];
        $arguments = [];

        foreach ($parameters as $parameter) {
            $name = $parameter->getName();
            $arguments[] = $source->$name ?? null;
        }

        // Crear una nueva instancia usando los argumentos del constructor
        $dtoInstance = $reflectionClass->newInstanceArgs($arguments);

        // Asignar propiedades adicionales si existen
        foreach ($reflectionClass->getProperties(ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PUBLIC) as $property) {
            $propertyName = $property->getName();
            if (property_exists($source, $propertyName)) {
                $setter = 'set' . ucfirst($propertyName);
                if (method_exists($dtoInstance, $setter)) {
                    $dtoInstance->$setter($source->$propertyName);
                }
            }
        }

        return $dtoInstance;
    }
}
