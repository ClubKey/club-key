<?php

declare(strict_types=1);

namespace App\Http\Controllers\CommunicationSubscriptions;

use App\Actions\CommunicationSubscriptions\ImportSubscriptionsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommunicationSubscriptions\ImportSubscriptionsRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

final class SubscriberImportController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function index(Request $request): Response
    {
        $club = $request->user()->currentClub;

        if (! $club) {
            abort(404, 'No current club selected.');
        }

        Gate::authorize('update', $club);

        return Inertia::render('Subscribers/Import', [
            'club' => $club,
        ]);
    }

    /**
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function store(
        ImportSubscriptionsRequest $request,
        ImportSubscriptionsAction $importSubscriptionsAction
    ): RedirectResponse {
        $club = $request->user()->currentClub;

        if (! $club) {
            abort(404, 'No current club selected.');
        }

        Gate::authorize('update', $club);

        $subscriptions = [];

        // Handle CSV file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $subscriptions = $this->parseCsvFile($file);
        } elseif ($request->has('subscriptions')) {
            // Handle manual entry
            $subscriptions = $request->input('subscriptions');
        }

        $result = $importSubscriptionsAction->handle(
            $club,
            $subscriptions,
            $request->input('source', 'import')
        );

        $errorsArray = array_map(
            fn ($error) => $error->toArray(),
            $result->errors
        );

        return back()->with([
            'success' => "Successfully imported {$result->success} subscriptions.",
            'error_messages' => $errorsArray,
        ]);
    }

    /**
     * @return array<int, array{contact_type: string, contact_value: string, topic: string}>
     */
    private function parseCsvFile(UploadedFile $file): array
    {
        $subscriptions = [];
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            return [];
        }

        // Skip header row if present
        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);

            return [];
        }

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 3) {
                continue;
            }

            // Expected format: contact_type, contact_value, topic, user_id (optional)
            $subscriptions[] = [
                'contact_type' => $row[0],
                'contact_value' => $row[1],
                'topic' => $row[2],
                'user_id' => isset($row[3]) && $row[3] !== '' ? (int) $row[3] : null,
            ];
        }

        fclose($handle);

        return $subscriptions;
    }
}
