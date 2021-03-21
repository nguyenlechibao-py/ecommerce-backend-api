<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Elasticquent\ElasticquentTrait;
use Exception;

class Category extends Model
{
    use ElasticquentTrait;

    protected $fillable = ['name', 'description', 'image'];

    protected $mappingProperties = [
        'name' => [
            'type' => 'string',
            'analyzer' => 'standard',
        ],
    ];

    public function search($title) {
        try {
            $result = self::searchByQuery(['match' => ['title' => $title]]);
            return response()->json([
                'data' => $result,
            ]);
        }
        catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
