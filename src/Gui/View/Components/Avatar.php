<?php

namespace Gui\View\Components;

use Demeter\Support\Image;
use Demeter\Support\Str;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Avatar extends Component
{
    public function __construct(
        public ?string $image = null,
        public ?string $content = null
    )
    {
        $this->content = $this->formatContent();
        $this->image = $this->renderAvatarImage();
    }

    protected function renderAvatarImage(): ?string
    {
        if(!$this->image){
            return null;
        }

        if(Image::isBase64Image($this->image)){
            if(Str::startsWith($this->image, 'data:image')){
                return "background-image: url('$this->image')";
            }

            $mimeType = Image::detectMimeTypeFromBase64($this->image);
            return "background-image: url('data:$mimeType;base64,$this->image')";
        }

        return "background-image: url('$this->image')";
    }

    protected function formatContent(): string
    {
        if(!$this->content){
            return '';
        }

        $words = preg_split('/\s+/', trim($this->content), -1, PREG_SPLIT_NO_EMPTY);

        if(count($words) === 1){
            $word = $words[0];
            return strlen($word) === 1 ? $word : $word[0] . $word[strlen($word) - 1];
        } else {
            return $words[0][0] . $words[count($words) - 1][0];
        }
    }

    public function render(): View|string
    {
        return view('gui::components.avatar');
    }
}