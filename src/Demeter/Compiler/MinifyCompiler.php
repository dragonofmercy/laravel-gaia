<?php
namespace Demeter\Compiler;

use Illuminate\View\Compilers\BladeCompiler;

class MinifyCompiler extends BladeCompiler
{
    protected array $ignoredPaths = [];

    /**
     * Init minify compiler
     *
     * @return void
     */
    public function initMinifyCompiler(): void
    {
        $this->compilers[] = 'Minify';
    }

    /**
     * Set ignored paths
     *
     * @param array $ignoredPaths
     * @return void
     */
    public function setIgnoredPaths(array $ignoredPaths): void
    {
        $this->ignoredPaths = $ignoredPaths;
    }

    /**
     * Minify html on blade compile
     *
     * @param string $expression
     * @return string
     */
    protected function compileMinify(string $expression) : string
    {
        if($this->ignoredPaths && null !== $this->getPath()){
            $path = str_replace('\\', '/', $this->getPath());
            foreach($this->ignoredPaths as $ignoredPath){
                if(str_contains($path, $ignoredPath)){
                    return $expression;
                }
            }
        }

        // The content inside these tags will be spared:
        $doNotCompressTags = ['script', 'pre', 'textarea', '?php'];
        $matches = [];

        foreach($doNotCompressTags as $tag){
            $regex = "!<{$tag}[^>]*?>.*?</$tag>!is";

            // It is assumed that this placeholder could not appear organically in your
            // output. If it can, you may have an XSS problem.
            $placeholder = "@@<'-placeholder-$tag'>@@";

            // Replace all the tags (including their content) with a placeholder, and keep their contents for later.
            $expression = preg_replace_callback($regex,
                function($match) use ($tag, &$matches, $placeholder){
                    $matches[$tag][] = $match[0];
                    return $placeholder;
                }, $expression
            );
        }

        // Remove whitespace (spaces, newlines and tabs)
        $expression = trim(preg_replace('/[\s\n\t]+/m', ' ', $expression));
        $expression = preg_replace('/(\>)\s*(\<)/m', '$1$2', $expression);

        // Iterate the blocks we replaced with placeholders beforehand, and replace the placeholders
        // with the original content.

        foreach($matches as $tag => $blocks){
            $placeholder = "@@<'-placeholder-$tag'>@@";
            $placeholderLength = strlen($placeholder);
            $position = 0;

            foreach($blocks as $block){
                $position = strpos($expression, $placeholder, $position);

                if($position === false){
                    throw new \RuntimeException("Found too many placeholders of type $tag in input string");
                }

                $expression = substr_replace($expression, $block, $position, $placeholderLength);
            }
        }

        // Remove HTML comment(s) except IE comment(s)
        return preg_replace('/\s*<!--(?!\[if\s).*?-->\s*/si', '', $expression);
    }
}