<?php

namespace Lamansky\Sniffs\Functions;

use PHP_CodeSniffer\Sniffs\AbstractPatternSniff;

class DeclarationSpaceSniff extends AbstractPatternSniff {
    protected function getPatterns () : array {
        return [
            'function abc (...)',
        ];
    }
}
