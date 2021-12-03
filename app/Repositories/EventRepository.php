<?php

namespace App\Repositories;

use App\Models\BaseModel;
use App\Models\Event;
use App\Models\EventImage;
use App\Models\EventSchedule;
use App\Repositories\Contracts\IEventRepository;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EventRepository extends GenericRepository implements IEventRepository
{
    /**
     * @var  BaseModel
     */
    protected $modelImage;

    public function __construct(EventImage $modelImage)
    {
        parent::__construct(app(Event::class));
        $this->modelImage = $modelImage;
    }

    public function create($model)
    {
        return DB::transaction(function () use ($model) {
            $schedules = [];
            if (isset($model['schedule']))
                $schedules = $model['schedule'];

            $images = [];
            if (isset($model['image']))
                $images = $model['image'];

            unset($model['schedule']);
            unset($model['image']);

            $data = $this->model->query()->create($model);

            foreach ($schedules as $schedule) {
                EventSchedule::query()->create([
                    'event_id' => $data->id,
                    'start_date' => Carbon::parse($schedule['startDate'])->format('d-m-Y H:i'),
                    'end_date' => Carbon::parse($schedule['endDate'])->format('d-m-Y H:i'),
                ]);
            }

            foreach ($images as $image) {
                $file = $image;
                if (file_exists($file)) {
                    $name = $file->getClientOriginalName();

                    $storagePath = Storage::put('event/images', $file);

                    EventImage::query()->create([
                        'event_id' => $data->id,
                        'file_path' => $storagePath,
                        'file_name' => (string)Str::of($storagePath)->basename(),
                        'file_name_original' => $name,
                    ]);
                }
            }

            return $data->onCreated();
        });
    }

    public function getImage($file)
    {
        $file = Str::replace('_', '.', $file);
        $data = $this->modelImage->query();
        $item = $data->where('file_name', $file)->firstOrFail();

        $file = Storage::get($item->file_path);
        $type = Storage::mimeType($item->file_path);
//        $path = storage_path('app') . '/' . $item->file_path;

        $response = response()->make($file, Response::HTTP_OK);
        $response->header("Content-Type", $type);
        $response->header('Content-disposition', 'attachment; filename="' . $item->file_name_original . '"');

        ob_end_clean();

        return $response;
    }

    public function publish($id)
    {
        $data = $this->model->query()
            ->with('tickets')
            ->where('organization_id', Auth::user()->organization_id)
            ->findOrFail($id);

        if (count($data->tickets) <= 0)
            throw new HttpException(Response::HTTP_OK, 'Make 1 ticket first');

        if (!$data->is_draft)
            throw new HttpException(Response::HTTP_OK, 'The event has been published');

        $data->update(['is_draft' => false]);

        return null;
    }
}
