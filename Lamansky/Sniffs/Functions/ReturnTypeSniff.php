<?php
namespace Lamansky\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\ScopeHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

class ReturnTypeSniff implements Sniff {
    public $allowUndeclaredReturnType = false;
    public $requireNativeDeclaration = false;

    public function register () : array {
        return [T_FUNCTION];
    }

    public function process (File $phpcs_file, $fn_pt) : void {
        $type_hint = FunctionHelper::findReturnTypeHint($phpcs_file, $fn_pt);
        if ($type_hint) {
            $type_hint_text = strtolower($type_hint->getTypeHint());
            $void = $this->returnsVoidOrNonVoid(true, $phpcs_file, $fn_pt);
            $non_void = $this->returnsVoidOrNonVoid(false, $phpcs_file, $fn_pt);
            if ($void
                && !$type_hint->isNullable()
                && strpos($type_hint_text, 'void') === false
            ) {
                if ($non_void) {
                    if (strpos($type_hint_text, 'mixed') === false) {
                        $phpcs_file->addError(
                            'Nullable return type declaration expected' . (
                                is_int($void)
                                    ? ', given the return statement on line ' . $void
                                    : ''
                            ) . '.',
                            $type_hint->getStartPointer(),
                            'NeedsNullableDeclaration'
                        );
                    }
                } else {
                    $phpcs_file->addError(
                        'Void return type declaration expected, given the '
                            . 'absence of return statements that return a value.',
                        $type_hint->getStartPointer(),
                        'NeedsVoidDeclaration'
                    );
                }
            } elseif (is_int($non_void)
                && strpos(strtolower($type_hint->getTypeHint()), 'void') !== false
            ) {
                $phpcs_file->addError(
                    'Non-void return type declaration expected, ' .
                        'given the return statement on line ' .
                        $non_void . '.',
                    $type_hint->getStartPointer(),
                    'NeedsNonVoidDeclaration'
                );
            }
        } elseif (!$this->allowUndeclaredReturnType
            && substr(FunctionHelper::getName($phpcs_file, $fn_pt), 0, 2) !== '__'
            && ($this->requireNativeDeclaration || !FunctionHelper::findReturnAnnotation($phpcs_file, $fn_pt))
        ) {
            if ($this->returnsVoidOrNonVoid(true, $phpcs_file, $fn_pt)) {
                $phpcs_file->addError(
                    'Void return type declaration expected.',
                    $fn_pt,
                    'MissingVoidDeclaration'
                );
            } else {
                $phpcs_file->addError(
                    'Return type declaration expected.',
                    $fn_pt,
                    'MissingDeclaration'
                );
            }
        }
    }

    /**
     * @return bool|int
     */
    protected function returnsVoidOrNonVoid (bool $void, File $phpcs_file, $fn_pt) {
        if (FunctionHelper::isAbstract($phpcs_file, $fn_pt)) {
            return false;
        }

        $tokens = $phpcs_file->getTokens();
        $first_pt_in_scope = $tokens[$fn_pt]['scope_opener'] + 1;

        $returns_other = !$void;

        for ($i = $first_pt_in_scope; $i < $tokens[$fn_pt]['scope_closer']; $i++) {
            if ($tokens[$i]['code'] !== T_RETURN) {
                continue;
            }

            if (!ScopeHelper::isInSameScope($phpcs_file, $i, $first_pt_in_scope)) {
                continue;
            }

            $next_pt = TokenHelper::findNextEffective($phpcs_file, $i + 1);
            if ($void === in_array($tokens[$next_pt]['code'], [T_NULL, T_SEMICOLON])) {
                return $tokens[$next_pt]['line'];
            } else {
                $returns_other = true;
            }
        }

        return !$returns_other;
    }
}
