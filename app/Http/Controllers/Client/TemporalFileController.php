<?php

namespace App\Http\Controllers\Client;


use App\Repositories\TemporalFileRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Class ArchivoTemporalController
 * @package Modules\General\Http\Controllers
 */
class TemporalFileController extends Controller
{
    /** @var  ArchivoTemporalRepository */
    private $temporalFileRepository;

    public function __construct(TemporalFileRepository $temporalFileRepo)
    {
        $this->archivoTemporalRepository = $temporalFileRepo;
    }

    /**
     * @OA\Post(
     *      path="/v1/uploads",
     *      summary="Store a newly created Temporal File",
     *      tags={"TemporalFile"},
     *      description="Store Temporal File",
     *      @OA\Parameter(
     *          name="file",
     *          description="file to be save",
     *          required=true,
     *          in="query"
     *      ),@OA\Parameter(
     *          name="model",
     *          description="model to which it belongs ",
     *          required=true,
     *          in="query"
     *      ),
     *      @OA\Response(
     *         response="200",
     *         description="ok",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="success",
     *                         type="boolean"
     *                     ),
     *                      @OA\Property(
     *                          property="object",
     *                          ref="#/components/schemas/TemporalFile"
     *                      ),
     *                      @OA\Property(
     *                          property="message",
     *                          type="string"
     *                      )
     *                 )
     *             )
     *         }
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validacion = Validator::make($request->all(), [
            'file' => 'nullable|file',
            'model' => 'required'
        ]);
        
        if ($validacion->fails()) {
            return response()->json(['errors' => $validacion->errors()], 422);
        }

        switch ($request->model) {
            default:
                return response()->json([
                    'message' => trans('msgs.msg_error_temporal_file_model', ['var' => $request->modelo]),
                    'type' => trans('msgs.type_error')
                ], 500);
        }

        if ($validacion->fails()) {
            return response()->json(['errors' => $validacion->errors()], 422);
        }
        try {
            $input = $request->all();
            $path = $request->file('file')->store('temp', 'public');
            $input['url'] = $path;
            $archivoTemporal = $this->archivoTemporalRepository->create($input);

            return response()->json([
                'message' => trans('msgs.msg_el_save_successfully', ['var' => trans('msgs.label_temporalFile')]),
                'type' => trans('msgs.type_success'),
                'object' => $archivoTemporal
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage() . " " . $e->getLine() . " " . $e->getFile(),
                'message' => trans('msgs.msg_error_contact_the_adminitrator'),
                'type' => trans('msgs.type_error')
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/v1/uploads_base64",
     *      summary="Store a newly created Temporal File in base64 encode",
     *      tags={"TemporalFile"},
     *      description="Store Temporal File",
     *      @OA\Parameter(
     *          name="file64",
     *          description="Base 64 from the file to be save",
     *          required=true,
     *          in="query"
     *      ),@OA\Parameter(
     *          name="model",
     *          description="model to which it belongs ",
     *          required=true,
     *          in="query"
     *      ),
     *      @OA\Response(
     *         response="200",
     *         description="ok",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="success",
     *                         type="boolean"
     *                     ),
     *                      @OA\Property(
     *                          property="object",
     *                          ref="#/components/schemas/TemporalFile"
     *                      ),
     *                      @OA\Property(
     *                          property="message",
     *                          type="string"
     *                      )
     *                 )
     *             )
     *         }
     *     )
     * )
     */
    public function uploads_base64(Request $request)
    {
        $validacion = Validator::make($request->all(), [
            'file64' => 'required',
            'model' => 'required'
        ]);

        if ($validacion->fails()) {
            return response()->json(['errors' => $validacion->errors()], 422);
        }
        try {
            $input = $request->all();
            $time = time();
            $path = "temp/$time.jpeg";
            \Storage::disk('public')->put($path, base64_decode($request->file64));
            $input['url'] = $path;
            $archivoTemporal = $this->archivoTemporalRepository->create($input);

            return response()->json([
                'message' => trans('msgs.msg_el_save_successfully', ['var' => trans('msgs.label_temporalFile')]),
                'type' => trans('msgs.type_success'),
                'object' => $archivoTemporal
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage() . " " . $e->getLine() . " " . $e->getFile(),
                'message' => trans('msgs.msg_error_contact_the_adminitrator'),
                'type' => trans('msgs.type_error')
            ], 500);
        }

    }
}
