<?php 
namespace App\Service;
use App\Entity\User;

    class FuncCommon{
        
/**
     * Procesa el texto para convertir menciones y hashtags en enlaces o estilos correspondientes.
     *
     * @param string $text El texto a procesar.
     * @return string El texto procesado.
     */
    public function mention($text)
    {
        $text = $this->processMentions($text);
        $text = $this->processHashtags($text);
        return $text;
    }

    /**
     * Procesa el texto para encontrar los hastags, que son los tags de la Review.
     *
     * @param string $text El texto a procesar.
     * @return [] Tags find.
     */
    public function findTags($text)
    {
        $tagsNames = [];
        $find = preg_match_all('/#[a-z0-9_]+/', $text, $tags);
        if($find) {
            foreach ($tags as $tag) {
                $tag = str_replace('#', '', $tag);
                $tagsNames[] = $tag;
            }
            return $tagsNames;
        } else {
            return false;
        }
        
    }

    /**
     * Procesa las menciones en el texto.
     *
     * @param string $text El texto a procesar.
     * @return string El texto con menciones convertidas a enlaces.
     */
    private function processMentions($text) 
    {
        $res = preg_replace("/@([A-Za-z0-9-_]+)/", '<a href="/profile/\1">@\1</a>', $text);
        
        return $res;
    }

    /**
     * Procesa los hashtags en el texto.
     *
     * @param string $text El texto a procesar.
     * @return string El texto con hashtags convertidos a un formato específico.
     */
    private  function processHashtags($text) 
    {
        $res = preg_replace('/#([A-Za-z0-9-_]+)/', '<a href="/tag/\1">#\1</a>', $text);
        return $res;
    }
}

?>