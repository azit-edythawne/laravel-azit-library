<?php

namespace Azit\Ddd\Job;

use Azit\Ddd\Arch\Data\Service\Notification\CreateNotificationService;
use Azit\Ddd\Arch\Domains\UseCases\BaseIterator;
use Azit\Ddd\Arch\Domains\UseCases\Entity\AuthEntity;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Nota 1:
 * El job trabaja con QUEUE_CONNECTION de tipo "sync"
 *
 * Nota 2:
 * El builder(Model) base requiere una tabla de la siguiente forma
 * create table notification (
 *  id          serial
 *  primary key,
 *  title       text                                          not null,
 *  description text                                          not null,
 *  route       text,
 *  active      smallint     default 1                        not null,
 *  created_by  varchar(150) default 'sys'::character varying not null,
 *  updated_by  varchar(150),
 *  deleted_by  varchar(150),
 *  created_at  timestamp(0) default CURRENT_TIMESTAMP        not null,
 *  updated_at  timestamp(0),
 *  deleted_at  timestamp(0)
 * );
 *
 * Nota 3:
 * Para convertir un Modelo a builder aplicar lo siguiente :
 * Notification::newQuery()
 */
class NotificationJob implements ShouldQueue, BaseIterator {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private CreateNotificationService $service;

    private AuthEntity $user;
    private string $param;
    private ?int $idNotification;
    private array $arguments;
    private ?array $idRolesReceiver;
    private ?int $idUserReceiver;

    /**
     * Creacion de notificaciones por array de roles
     * @param Builder $base
     * @param Builder $notification
     * @param int $idNotification
     * @param AuthEntity $user
     * @param array $args
     * @param mixed $idRequest
     * @param mixed $roles
     * @return void
     */
    public static function byRoles(Builder $base, Builder $notification, int $idNotification, AuthEntity $user, array $args, mixed $idRequest, mixed $roles){
        try {
            NotificationJob::dispatch($idNotification, $user, $args, $idRequest, $roles);
        } catch (Exception $exception) {

        }
    }

    /**
     * Creaciion de notificaciones por id de usuario
     * @param Builder $base
     * @param Builder $notification
     * @param int $idNotification
     * @param AuthEntity $user
     * @param array $args
     * @param mixed $idRequest
     * @param int $idReceiver
     * @return void
     */
    public static function byUser(Builder $base, Builder $notification, int $idNotification, AuthEntity $user, array $args, mixed $idRequest, int $idReceiver){
        try {
            NotificationJob::dispatch($idNotification, $user, $args, $idRequest, null, $idReceiver);
        } catch (Exception $exception) {

        }
    }


    /**
     * Constructor
     * @param int $idNotification
     * @param AuthEntity $userCurrent
     * @param array $args
     * @param mixed $param
     * @param mixed|null $idRolesReceiver
     * @param int|null $idUserReceiver
     */
    public function __construct(Builder $base, Builder $notification, int $idNotification, AuthEntity $userCurrent, array $args, mixed $param, mixed $idRolesReceiver = null, int $idUserReceiver = null) {
        $this -> service = new CreateNotificationService($this, $base, $notification);
        $this -> setRoles($idRolesReceiver);
        $this -> idUserReceiver = $idUserReceiver;
        $this -> param = strval($param);

        $this -> idNotification = $idNotification;
        $this -> user = $userCurrent;
        $this -> arguments = $args;
    }

    /**
     * Asignación de roles a variable global
     * @param mixed $roles
     * @return void
     */
    public function setRoles(mixed $roles) : void {
        if ($roles == null) {
            $this -> idRolesReceiver = null;
        }

        if (is_array($roles)) {
            $this -> idRolesReceiver = $roles;
        }

        $this -> idRolesReceiver = Arr::wrap($roles);
    }

    public function transform(): Collection {
        return collect([
            'id' => $this -> idNotification,
            'args' => $this -> arguments,
            'id_roles_receiver' => $this -> idRolesReceiver,
            'id_user_receiver' => $this -> idUserReceiver,
            'param' => $this -> param,
            'user' => $this -> user
        ]);
    }

    public function feedback(mixed $out) : array {
        return [];
    }

    public function handle() :void {
        $this -> service -> execute();
    }

}