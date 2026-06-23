<?php
declare(strict_types=1);

namespace CustoDesk\Parser\Spoiler;

use League\CommonMark\Node\Inline\AbstractInline;
use League\CommonMark\Node\Inline\DelimitedInterface;

final class Spoiler extends AbstractInline implements DelimitedInterface
{
    private string $openingDelimiter;
    private string $closingDelimiter;

    public function __construct()
    {
        parent::__construct();

        $this->openingDelimiter = '>!';
        $this->closingDelimiter = '!<';
    }

    public function getOpeningDelimiter(): string
    {
        return $this->openingDelimiter;
    }

    public function getClosingDelimiter(): string
    {
        return $this->closingDelimiter;
    }
}
