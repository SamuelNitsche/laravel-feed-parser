<?php namespace ArandiLopez\Feed\Adapters;

use Illuminate\Support\Str;
use ArandiLopez\Feed\Adapters\SimplePieAuthorAdapter as Author;

use JsonSerializable;
use ArrayAccess;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

class SimplePieItemAdapter implements JsonSerializable, Jsonable, ArrayAccess, Arrayable {

    protected $item;

    function __construct($item)
    {
        $this->item = $item;
    }

    public function getRawItem()
    {
        return $this->item;
    }

    public function __get($attribute)
    {
        if( $attribute === 'author' ){
            return $this->getAuthor();
        }

        if ( $attribute === 'authors' ) {
            return $this->getAuthors();
        }
        $attr = Str::snake($attribute);
        $method = 'get_'.$attr;

        return $this->item->$method();
    }

    public function getAuthor()
    {
        if( $author = $this->item->get_author()){
            return new Author( $author );
        }
        return null;
    }

    public function getAuthors()
    {
        $authors = [];
        foreach ($this->item->get_authors() as $author) {
            array_push($authors, new Author($author));
        }
        return $authors;
    }

    public function toJson($option = 0)
    {
        return $this->toArray();
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Return basic item data. Authors in array
     * @method toArray
     * @return [type]
     */
    public function toArray()
    {
        return [
            'title'         => $this->title,
            'description'   => $this->description,
            'content'       => $this->content,
            'authors'       => array_map(function ($author) {
                                            return $author->toArray();
                                        }, isset($this->authors) ? $this->authors : []),
            'date'          => $this->date,
            'permalink'     => $this->permalink,
        ];
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->$offset);
    }

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * Set the value for a given offset.
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    /**
     * Unset the value for a given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->$offset);
    }
}
