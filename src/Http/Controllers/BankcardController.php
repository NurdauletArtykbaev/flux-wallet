<?php

namespace Nurdaulet\FluxWallet\Http\Controllers;

use Nurdaulet\FluxWallet\Http\Resources\BankcardsResource;
use Illuminate\Http\Request;
use Nurdaulet\FluxWallet\Repositories\BankcardRepository;
use Nurdaulet\FluxWallet\Services\BankcardService;
use Nurdaulet\FluxWallet\Services\Payment\Facades\Payment;

class BankcardController
{
    public function __construct(private BankcardRepository $bankcardRepository, private BankcardService $bankcardService)
    {
    }

    public function index(Request $request)
    {
        return BankcardsResource::collection($this->bankcardRepository->getByUser($request->user()));
    }

    public function topUp($id, Request $request)
    {
        $user = $request->user();
        $amount = $request->input('amount', 200);
        $user = config('flux-wallet.models.user')::findOrFail($user->id);

        $this->bankcardService->topUp($user,$id, $amount);

        return response()->noContent();
    }

    public function getRedirectLink(Request $request)
    {
        $url = Payment::getUrlForCardAddition($request->user(), $request->input('amount', 200));
        return response()->json(['data' => compact('url')]);
    }

    public function destroy($id, Request $request)
    {
       $this->bankcardService->delete($request->user(), $id);
        return response()->noContent();
    }
}
