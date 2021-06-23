<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Exception;
use JsonException;

class UsuarioController extends CadastroController
{
    public $model = Usuario::class;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['cadastrarUsuario']]);   
    }

    public function usuarios()
    {
       return $this->MostrarTodos($this->model);
    }

    public function usuario($id)
    {                
       return $this->MostrarUm($id, $this->model);
    }

    public function cadastrarUsuario(Request $request)
    {
        // validação
        $this-> validate($request, [
            'cpf' =>    'required|unique:usuarios,cpf',
            'email' =>  'required|email|unique:usuarios,email',
        ]);
        
        try{

            $this->validaCPF($request->cpf);

            return $this->Cadastrar($request,$this->model,'cpf');
            
        }catch( Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function atualizarUsuario($id, Request $request)
    {
         // validação
         $this-> validate($request, [
            'cpf' =>    'required',
        ]);

        try{

            $this->validaCPF($request->cpf);

            return $this->Atualizar($id,$request, $this->model,'cpf');
        }catch( Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deletarUsuario($id)
    {
        return $this->Deletar($id, $this->model);
        
    }

    
    function validaCPF($cpf) {
 
        $cpf = preg_replace( '/[^0-9]/is', '', $cpf );
         
        if (strlen($cpf) != 11) {

            return throw new JsonException('CPF deve conter 11 numeros!', 500);
        }
    
        if (preg_match('/(\d)\1{10}/', $cpf)) {
        
         return throw new JsonException('CPF não pode ter sequência de digitos repetidos!', 500);
        }
    
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
            
                return throw new JsonException( 'CPF inválido!', 500);
            }
        }
        return true;
    
    }
}
