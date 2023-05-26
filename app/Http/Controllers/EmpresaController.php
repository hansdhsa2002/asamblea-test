<?php

namespace App\Http\Controllers;

use App\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function selectEmpresas(Request $request){
        $term = $request->term ? : '';
        $tags = Empresa::where('razon_social', 'like', $term.'%')
            ->orWhere('nit_sin_digito', 'like', $term.'%')
            ->limit(5)->get();
        $valid_tags = [];
        foreach ($tags as $id => $tag) {
            $valid_tags[] = ['id' => $tag->nit_sin_digito, 'text' => "$tag->nit_sin_digito - $tag->razon_social"];
        }
        return $valid_tags;
    }
}
