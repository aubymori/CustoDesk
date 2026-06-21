<?php
namespace CustoDesk\TemplateUtils;

class Modules
{
    private array $modules = [];

    public function add(string $module): string
    {
        if (false === array_search($module, $this->modules, true))
        {
            $this->modules[] = $module;
        }
        return "";
    }

    public function get(): array
    {
        return $this->modules;
    }
}