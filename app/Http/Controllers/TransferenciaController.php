<?php

namespace App\Http\Controllers;

use App\Models\Transferencia;
use Illuminate\Http\Request;
use Exception;
use JsonException;
use App\Http\Controllers\AutorizacaoController;
use GuzzleHttp\Client as GuzzleHttpClient;
use PhpParser\Node\Expr\Cast\Object_;
use vendor\guzzlehttp\guzzle\src\Client;
class TransferenciaController extends CadastroController
{
    private $usuario;
    private $empresa;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['cadastrarUsuario']]);   
    }

    // public function usuarios()
    // {
    //    return $this->MostrarTodos($this->model);
    // }

    // public function usuario($id)
    // {                
    //    return $this->MostrarUm($id, $this->model);
    // }
    
    public function deposito(Request $request)
    {
        // validação
        $this-> validate($request, [
            'payer' =>   'required',
            'payee' =>   'required',
            'value' =>   'required',
        ]);
        
        try{
            $cadastro = new Transferencia();            
            $this->verificaTransferencia($request);
            
            $cadastro->payer    = $request->payer;
            $cadastro->payee    = $request->payee;
            $cadastro->value    = $request->value;
            $cadastro->save();

        }catch(Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json($cadastro,201);
    }

    public function verificaTransferencia($request){

        $deposito = array();
        $depositante = $this->verificaDepositante($request,"Payer");
        $depositante->valid = 'cpf';

        if( strripos($request->url(), "/usuario/deposito")){

            $deposito        = $this->verificaUsuario($request->payee,"Payee");
            $deposito->valid = 'cpf';
        }
        else {

            $deposito        = $this->verificaEmpresa($request->payee,"Payee");
            $deposito->valid = 'cnpj';  
        }

        if($this->verificaSaldo($request,$depositante))
        {
            $return = $this->atualizarSaldo($deposito,$depositante);
        }

        return $return;

    }

    public function verificaSaldo($request,$depositante)
    {
        // compara o saldo do cadastro com o da requisição
        if($depositante->saldo < $request->value){
            return throw new JsonException('Payer não possui saldo para essa transação!', 500);
        }else{
            if($this->autorizaTransacao('https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6') == 200){
               return true;
            }
            else{
                return throw new JsonException('Transação não autorizada pelo serviço externo!', 500);
            }
        }
    }

    public function autorizaTransacao($uri)
    {
        try{
            $client = new GuzzleHttpClient(['base_uri' => $uri]);
            $response = $client->request('GET');
        }catch(JsonException $e){
            return response()->json(['error' => "Erro ao comunicar com serviço externo!"], 500);
        }
        return $response->getStatusCode();
    }

    public function verificaDepositante($request)
    {
        $oAuth  = new AutorizacaoController();
        $aAuth  = $oAuth->me()->getData();

       $user = $this->verificaUsuario($request->payer, "Payer");

        if($user->id == $aAuth->id && $user->email == $aAuth->email)
        {
            return $user;
        }else{
            return throw new JsonException('Payer não possui autoriazação para essa transação!', 401);
        }
    }

    public function verificaEmpresa($request,$tipo )
    {
        
        $oEmpresa = new EmpresaController();
        $a_return = $oEmpresa->empresa($request)->getData();

        if(isset($a_return->error)){
            return throw new JsonException($tipo.":".$request." informado não localizado", 401);
        }else{
            return $a_return;
        }
        
    }

    public function verificaUsuario($request,$tipo)
    {
        $oUsuario = new UsuarioController();
        $a_return = $oUsuario->usuario($request)->getData();

        if(isset($a_return->error)){
            return throw new JsonException($tipo.":".$request." informado não localizado", 401);
        }else{

            return $a_return;
        }
        
    }

    public function atualizarSaldo($deposito,$depositante)
    {
   
        try{
            
            $deposito->saldo =+ $depositante->saldo;
            $depositante->saldo = ($depositante->saldo - $depositante->saldo);

           
            $cadastro = $this->model::find($deposito->id);

            $cadastro->email    = $deposito->email;
            $cadastro->nome     = $deposito->usuario;
            $cadastro->password = Hash::make('1234');
            $cadastro->saldo     = $deposito->saldo;
    
            $cadastro->save();  
            die(print_r($cadastro,true));
            $this->Atualizar($deposito->id    ,$deposito,   $this->model,   $deposito->valid);
            $this->Atualizar($depositante->id,$depositante, $this->model,   $depositante->valid);
            
            return true;
        }catch( Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deletarUsuario($id)
    {
        return $this->Deletar($id, $this->model);
        
    }


}
