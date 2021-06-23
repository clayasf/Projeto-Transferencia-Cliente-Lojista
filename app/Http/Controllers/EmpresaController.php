<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use JsonException;
use Exception;

class EmpresaController extends CadastroController
{

    public $model = Empresa::class;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['cadastrarEmpresa']]);   
    }

    public function empresas()
    {
       return $this->MostrarTodos($this->model);
    }

    public function empresa($id)
    {        
        return $this->MostrarUm($id, $this->model);
    }

    public function cadastrarEmpresa(Request $request)
    {

        // validação
        $this-> validate($request, [
        'email' => 'required|email|unique:empresas,email',
        'cnpj' => 'required|unique:empresas,cnpj',
        ]);

        
        try{

            $this->validar_cnpj($request->cnpj);

            return $this->Cadastrar($request,$this->model,'cnpj');
            
        }catch( Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function atualizarEmpresa($id, Request $request)
    {
         // validação
         $this-> validate($request, [
            'cnpj' =>    'required',
        ]);

        try{

            $this->validar_cnpj($request->cnpj);            
            return $this->Atualizar($id,$request, $this->model,'cnpj');

        }catch( Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deletarEmpresas($id)
    {
        return $this->Deletar($id, $this->model);
    }

    

    function validar_cnpj($cnpj)
    {
        $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);
        
        if (strlen($cnpj) != 14)
            return throw new JsonException('CNPJ deve conter 14 números!', 500);

        if (preg_match('/(\d)\1{13}/', $cnpj))
            return throw new JsonException('CNPJ não pode ter sequência de digitos repetidos!', 500);

        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++)
        {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

        if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto))
            return throw new JsonException( 'CNPJ com primeiro digito verificador inconsistente!', 500);

        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++)
        {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

        return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
    }
}  
