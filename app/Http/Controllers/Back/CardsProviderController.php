<?php

namespace App\Http\Controllers\Back;

use App\Exceptions\CustomException;
use App\Models\CardsProvider;
use App\Repositories\CardsProviderRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class CardsProviderController extends Controller
{
    /** @var  CardsProviderRepository */
    private $cardsProviderRepository;

    public function __construct(CardsProviderRepository $cardsProviderRepo)
    {
        $this->cardsProviderRepository = $cardsProviderRepo;
    }

    public function index(Request $request)
    {
        $userId = $this->cardsProviderRepository->getUser();

        if ($userId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        $cardsProviders = $this->cardsProviderRepository->all();

        return response()->json(['data' => $cardsProviders], 200);
    }

    public function cuentasMadresXProveeedor($card_provider_id)
    {
        try {
            $userId = $this->cardsProviderRepository->getUser();

            if ($userId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            $motherCards = $this->cardsProviderRepository->cuentasMadres($card_provider_id);

            return response()->json(['data' => $motherCards], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.msg_card_provider_error', 1)]),
                'type' => trans('msgs.type_error'),
                'object' => $motherCards
            ], 404);
        } catch (CustomException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'type' => trans('msgs.type_error')
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => trans('msgs.msg_error_contact_the_adminitrator'),
                'type' => trans('msgs.type_error'),
                'object' => $e
            ], 500);
        }
    }

    public function getAccountsByProvider($card_provider_id)
    {
        try {
            $userId = $this->cardsProviderRepository->getUser();

            if ($userId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            $accounts = $this->cardsProviderRepository->getAccountsByProvider($card_provider_id);

            return response()->json(['data' => $accounts], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.msg_card_provider_error', 1)]),
                'type' => trans('msgs.type_error'),
                'object' => $accounts
            ], 404);
        } catch (CustomException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'type' => trans('msgs.type_error')
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => trans('msgs.msg_error_contact_the_adminitrator'),
                'type' => trans('msgs.type_error'),
                'object' => $e
            ], 500);
        }
    }

    public function getTransactionsCollectionAccountsByProvider($card_provider_id)
    {
        try {
            $userId = $this->cardsProviderRepository->getUser();

            if ($userId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            $accounts = $this->cardsProviderRepository->getTransactionsCollectionAccountsByProvider($card_provider_id);

            return response()->json(['data' => $accounts], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.msg_card_provider_error', 1)]),
                'type' => trans('msgs.type_error'),
                'object' => $accounts
            ], 404);
        } catch (CustomException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'type' => trans('msgs.type_error')
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => trans('msgs.msg_error_contact_the_adminitrator'),
                'type' => trans('msgs.type_error'),
                'object' => $e
            ], 500);
        }
    }

    public function getTransactionsCardLoadByProvider($card_provider_id)
    {
        try {
            $userId = $this->cardsProviderRepository->getUser();

            if ($userId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            $accounts = $this->cardsProviderRepository->getTransactionsCardLoadByProvider($card_provider_id);

            return response()->json(['data' => $accounts], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.msg_card_provider_error', 1)]),
                'type' => trans('msgs.type_error'),
                'object' => $accounts
            ], 404);
        } catch (CustomException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'type' => trans('msgs.type_error')
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => trans('msgs.msg_error_contact_the_adminitrator'),
                'type' => trans('msgs.type_error'),
                'object' => $e
            ], 500);
        }
    }

    public function globalBalance(Request $request)
    {
        $userId = $this->cardsProviderRepository->getUser();

        if ($userId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        $cardsProviders = $this->cardsProviderRepository->globalBalance();

        return response()->json(['data' => $cardsProviders], 200);
    }

    public function filtroAllCardLoad($card_provider_id, Request $request)
    {
        $userId = $this->cardsProviderRepository->getUser();

        if ($userId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        $filter = $request->filter ? json_decode($request->filter) : [];
        $orderBy = $request->orderBy ? $request->orderBy : 'created_at';
        $direction = $request->direction ? $request->direction : 'ASC';
        $paginate = 10;
        if (!empty($request['paginate']) && $request['paginate'] != null)
            $paginate = $request['paginate'];
        try {
            $paginado =  $this->cardsProviderRepository->filtroAllCardLoad($filter, $orderBy, $direction, $paginate, $request, $card_provider_id);
            $result = $this->cardsProviderRepository->filterAdminCardLoad($paginado->items());

            $paginado = $paginado->toArray();
            $paginado['data'] = $result;

            return response()->json($paginado, 200);
        } catch (QueryException $e) {
            return response()->json([
                'message' => trans('msgs.msg_error_filter_column_no_exist'),
                'type' => trans('msgs.type_error')
            ], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account_reload', 1)]),
                'type' => trans('msgs.type_error'),
                // 'object' => $data
            ], 404);
        } catch (CustomException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'type' => trans('msgs.type_error')
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => trans('msgs.msg_error_contact_the_adminitrator'),
                'type' => trans('msgs.type_error'),
                'object' => $e
            ], 500);
        }
    }
}
