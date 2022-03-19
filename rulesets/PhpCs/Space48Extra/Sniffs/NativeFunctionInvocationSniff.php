<?php declare(strict_types = 1);

namespace Space48\CodeQuality\RuleSets\PhpCs\Space48Extra\Sniffs;

use PHP_CodeSniffer\Files\File as PHP_CodeSniffer_File;
use PHP_CodeSniffer\Sniffs\Sniff as PHP_CodeSniffer_Sniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff;

class NativeFunctionInvocationSniff extends ForbiddenFunctionsSniff
{

    const SET_COMPILER_OPTIMIZED = 'compiler_optimized';
    const SET_INTERNAL = 'internal';

    public $set = self::SET_INTERNAL;

    /**
     * @return array
     */
    public function register()
    {
        if ($this->set === self::SET_INTERNAL) {
            $this->forbiddenFunctions = $this->formatForForbidden($this->getAllInternalFunctionsNormalized());
        } else {
            $this->forbiddenFunctions = $this->formatForForbidden($this->getAllCompilerOptimizedFunctionsNormalized());
        }

        return parent::register();
    }

    /**
     * @param array $functionNames
     * @return array
     */
    private function formatForForbidden(array $functionNames): array
    {
        $forbiddenFunctions = [];
        foreach (array_keys($functionNames) as $name) {
            $forbiddenFunctions[$name] = $name;
        }

        return $forbiddenFunctions;
    }

    /**
     * @param \PHP_CodeSniffer\Files\File $phpcsFile
     * @param int $stackPtr
     * @param string $functionName
     * @param null|string $pattern
     */
    protected function addError($phpcsFile, $stackPtr, $functionName, $pattern = null)
    {
        $phpcsFile->addError($this->getErrorMessage(), $stackPtr, 'Encountered', [$functionName, $functionName]);
    }

    /**
     * @return string
     */
    private function getErrorMessage(): string
    {
        return 'Native function "%s" must be invoked with root namespace: "\%s"';
    }

    /**
     * @return array<string, true> normalized function names of which the PHP compiler optimizes
     */
    private function getAllCompilerOptimizedFunctionsNormalized(): array
    {
        return $this->normalizeFunctionNames([
            // @see https://github.com/php/php-src/blob/PHP-7.4/Zend/zend_compile.c "zend_try_compile_special_func"
            'array_key_exists',
            'array_slice',
            'assert',
            'boolval',
            'call_user_func',
            'call_user_func_array',
            'chr',
            'count',
            'defined',
            'doubleval',
            'floatval',
            'func_get_args',
            'func_num_args',
            'get_called_class',
            'get_class',
            'gettype',
            'in_array',
            'intval',
            'is_array',
            'is_bool',
            'is_double',
            'is_float',
            'is_int',
            'is_integer',
            'is_long',
            'is_null',
            'is_object',
            'is_real',
            'is_resource',
            'is_string',
            'ord',
            'strlen',
            'strval',
            // @see https://github.com/php/php-src/blob/php-7.2.6/ext/opcache/Optimizer/pass1_5.c
            'constant',
            'define',
            'dirname',
            'extension_loaded',
            'function_exists',
            'is_callable',
        ]);
    }

    /**
     * @return array<string, true> normalized function names of all internal defined functions
     */
    private function getAllInternalFunctionsNormalized(): array
    {
        return $this->normalizeFunctionNames(get_defined_functions()['internal']);
    }

    /**
     * @param string[] $functionNames
     *
     * @return array<string, true> all function names lower cased
     */
    private function normalizeFunctionNames(array $functionNames): array
    {
        foreach ($functionNames as $index => $functionName) {
            $functionNames[strtolower($functionName)] = true;
            unset($functionNames[$index]);
        }

        return $functionNames;
    }
}
