<?php

namespace App\Http\Controllers;

use Exception;
use Hamcrest\Arrays\IsArray;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\hash;

Abstract class CadastroController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','cadastrar']]);         
    }

    public function MostrarTodos($model)
    {
        
        try{
            $aCadastro = array();
            $aCadastro =  $model::all();

            if($aCadastro->isNotEmpty())
            {
                return response()->json($aCadastro);
            }else{        
                return throw new Exception('Cadastro vazio!');
            }
        }catch(Exception $e){
            return response()->json(['error' => $e->getMessage()], 200);
        }
    }

    public function MostrarUm($id, $model)
    {        
        try{
            $aCadastro = array();
            $aCadastro =  $model::findOrFail($id);
            
            return response()->json($aCadastro);
            
        }catch(Exception $e){
            return response()->json(['error' => "Id:'$id' não encontrado!"], 200);
        }
    }

    public function cadastrar($request, $model, $doc)
    {
        try{

            // validação
            $this-> validate($request, [
            'nome' => 'required|min:5|max:40',
            'password' => 'required',

            ]);

            $cadastro = new $model;

            $cadastro->email    = $request->email;
            $cadastro->nome     = $request->nome;
            $cadastro->password = Hash::make($request->password);
            $cadastro->$doc     = $request->$doc;

            $cadastro->save();

        }catch(QueryException $e){
            return response()->json(['error' => 'Erro ao inserir Cadastro!'], 500);
        }

        return response()->json($cadastro,201);
    }

    public function Atualizar($id, Request $request, $model, $doc)
    {
        // validação
        die(print_r('teste',true));
        $this-> validate($request, [
            'password'  =>    'required',
            'email'     =>    'required',
        ]);

        $cadastro = $model::find($id);

        $cadastro->email    = $request->email;
        $cadastro->nome     = $request->usuario;
        $cadastro->password = Hash::make($request->password);
        $cadastro->saldo     = $request->saldo;
        $cadastro->$doc     = $request->$doc;

        $cadastro->save();   

        return response()->json($cadastro);
    }

    public function Deletar($id, $model)
    {
        try{
            $cadastro = $model::findOrFail($id);

            $cadastro->delete();  

            return response()->json(["cadastro" => "Deletado com sucesso"],200);

        }catch(Exception $e){

            return response()->json(['error' => 'Erro ao deletar cadastro!'], 500);
        }
    }
}
