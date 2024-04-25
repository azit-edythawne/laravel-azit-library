<?php

namespace Azit\Ddd\Arch\Data\Service\Notification;

use Azit\Ddd\Arch\Constant\MessageConstant;
use Azit\Ddd\Arch\Data\Service\BaseLocalService;
use Azit\Ddd\Arch\Domains\UseCases\BaseIterator;
use Azit\Ddd\Arch\Domains\UseCases\Entity\AuthEntity;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateNotificationService extends BaseLocalService {

    private Builder $base;
    private Builder $notification;

    /**
     * Constructor
     * @param BaseIterator $iterator
     * @param Builder $base
     * @param Builder $notification
     */
    public function __construct(BaseIterator $iterator, Builder $base, Builder $notification){
        parent::__construct($iterator);
        $this -> base = $base;
        $this -> notification = $notification;
    }

    public function execute(): void {
        DB::beginTransaction();

        $data = $this -> iterator -> transform();

        try {
            /** @var AuthEntity $user */
            $user = $data -> get('user');
            $id = $data -> get('id');
            $args = $data -> get('args');
            $param = $data -> get('param');

            $idUserReceivers = [];
            $baseNotification = $this -> getBaseNotification($id, $args);

            if ($data -> get('id_roles_receiver') != null) {
                $idUserReceivers = $data -> get('id_roles_receiver');
            }

            if ($data -> get('id_user_receiver') != null) {
                $idUserReceivers = array_merge($idUserReceivers, $data -> get('id_user_receiver'));
            }

            $builder = collect($idUserReceivers) -> map(function (int $idUserReceiver) use ($baseNotification, $args, $user, $param){
                return [
                    'description' => Str::replaceArray('%s', $args, $baseNotification['description']),
                    'receiver_id' => $idUserReceiver,
                    'created_by' => $user -> getId(),
                    'title' => $baseNotification -> title,
                    'route' => $baseNotification -> route,
                    'sender_id' => $user -> getId(),
                    'active' => true,
                    'param' => $param
                ];
            }) -> all();

            $this -> notification -> insert($builder);
            $this -> iterator -> feedback(true);

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
        }
    }

    /**
     * @param int $id
     * @param array $args
     * @return Model|Collection
     * @throws Exception
     */
    private function getBaseNotification(int $id, array $args) : Model|Collection {
        $model = $this -> base -> findOrFail($id);

        if ($model == null || substr_count($model -> description, '%s') != count($args)) {
            throw new Exception(MessageConstant::EXCEPTION_DATA_REQUIRED);
        }

        return $model;
    }

}