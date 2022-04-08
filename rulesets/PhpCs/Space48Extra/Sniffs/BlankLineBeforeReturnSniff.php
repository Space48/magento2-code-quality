<?php

namespace Space48\CodeQuality\RuleSets\PhpCs\Space48Extra\Sniffs;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File as CodeSnifferFile;

class BlankLineBeforeReturnSniff implements Sniff
{
    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array(
        'PHP',
        'JS',
    );

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_RETURN);
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param CodeSnifferFile $phpcsFile All the tokens found in the document.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    public function process(CodeSnifferFile $phpcsFile, $stackPtr)
    {
        $tokens          = $phpcsFile->getTokens();
        $current         = $stackPtr;
        $previousLine    = $tokens[$stackPtr]['line'] - 1;
        $prevLineTokens  = array();

        while ($current >= 0 && $tokens[$current]['line'] >= $previousLine) {
            if ($tokens[$current]['line'] == $previousLine
                && $tokens[$current]['type'] !== 'T_WHITESPACE'
                && $tokens[$current]['type'] !== 'T_COMMENT'
            ) {
                $prevLineTokens[] = $tokens[$current]['type'];
            }
            $current--;
        }

        if (isset($prevLineTokens[0])
            && ($prevLineTokens[0] === 'T_OPEN_CURLY_BRACKET'
                || $prevLineTokens[0] === 'T_COLON')
        ) {
            return;
        } else if (count($prevLineTokens) > 0) {
            $fix = $phpcsFile->addFixableError(
                'Missing blank line before return statement',
                $stackPtr,
                'MissedBlankLineBeforeRetrun'
            );

            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                $i = 1;
                while($tokens[$stackPtr-$i]['type'] == "T_WHITESPACE") {
                    $i++;
                }
                $phpcsFile->fixer->addNewLine($stackPtr-$i);
                $phpcsFile->fixer->endChangeset();
            }
        }

        return;
    }
}
