<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Clubes;
use App\Models\Recursos;
use Illuminate\Http\Request;

class ClubesController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $clubes = Clubes::all();

        // certificando que os valores serão apresentados conforme solicitado
        $clubesFormatados = $clubes->map(function ($clube) {
            $clube->saldo_disponivel = formatoMonetarioApiCBC($clube->saldo_disponivel);
            return $clube;
        });

        return response()->json($clubesFormatados, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();

        // Convertendo o valor de saldo_disponivel para o formato com ponto decimal
        $data['saldo_disponivel'] = str_replace(',', '.', $data['saldo_disponivel']);

        Clubes::create($data);

        return response()->json('ok', 200);
    }

    /**
     * Consome recursos da tabela de recursos e do clube
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function consumirRecurso(Request $request)
    {
        $clubeId = $request->input('clube_id');
        $recursoId = $request->input('recurso_id');
        $valorConsumo = str_replace(',', '.', $request->input('valor_consumo'));

        // Buscar o clube e recurso no banco de dados
        $clube = Clubes::find($clubeId);
        $recurso = Recursos::find($recursoId);

        // Verificar se o saldo disponível do clube é suficiente para o consumo
        if ($clube->saldo_disponivel < $valorConsumo) {
            return response()->json('O saldo disponível do clube é insuficiente', 400);
        }

        // Atualizar os saldos
        $saldoAnterior = $clube->saldo_disponivel;
        $saldoAtual = $saldoAnterior - $valorConsumo;

        // Atualizar o saldo do clube
        $clube->saldo_disponivel = $saldoAtual;
        $clube->save();

        // Atualizar o saldo do recurso
        $recurso->saldo_disponivel -= $valorConsumo;
        $recurso->save();

        // Retornar a resposta com os saldos atualizados
        return response()->json(
            [
                'clube' => $clube->clube,
                'saldo_anterior' => formatoMonetarioApiCBC($saldoAnterior),
                'saldo_atual' => formatoMonetarioApiCBC($saldoAtual)
            ],
            200
        );
    }

}
