<?php

namespace Phpsa\LaravelApiController\Traits;

use Illuminate\Http\Response as Res;
use Illuminate\Pagination\LengthAwarePaginator;

Class Response {

	/**
     * HTTP header status code.
     *
     * @var int
     */
	protected $statusCode = Res::HTTP_OK;

	/**
     * Resource key for an item.
     *
     * @var string
     */
    protected $resourceKeySingular = 'data';

    /**
     * Resource key for a collection.
     *
     * @var string
     */
    protected $resourceKeyPlural = 'data';

	/**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param $message
     * @return self
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
	}

	/**
     * Respond with a given item.
     *
     * @param $item
     *
     * @return Res
     */
    protected function respondWithOne($item)
    {
        return $this->respond([
            'status'      					=> 'success',
            'status_code' 					=> Res::HTTP_OK,
            $this->resourceKeySingular      => $item,
        ]);
    }

    /**
     * Respond with a given collection of items.
     *
     * @param $items
     * @param int $skip
     * @param int $limit
     *
     * @return Res
     */
    protected function respondWithMany($items)
    {
        return $this->respond([
            'status'      					=> 'success',
            'status_code' 					=> Res::HTTP_OK,
            $this->resourceKeyPlural        => $items,
        ]);
    }

    /**
     * Created Response.
     *
     * @param int|array    $id      id of insterted data
     * @param string $message message to respond with
     *
     * @return Res
     */
    public function respondCreated($id = null, $message = null)
    {
        $response = [
            'status'      		=> 'success',
            'status_code' 		=> Res::HTTP_CREATED,
        ];
        if ($message !== null) {
            $response['message'] = $message;
        }
        if ($id !== null) {
            if (is_scalar($id)) {
                $response[$this->resourceKeySingular] = $id;
            } else {
                $response[$this->resourceKeyPlural] = $id;
            }
        }

        return $this->respond($response);
    }

    /**
     * @param LengthAwarePaginator $paginate
     * @param $data
     * @return Res
     */
    protected function respondWithPagination(LengthAwarePaginator $paginator)
    {
        return $this->respond([
            'status'      					=> 'success',
            'status_code' 					=> Res::HTTP_OK,
            'paginator' => [
                'total_count'  => $paginator->total(),
                'total_pages'  => ceil($paginator->total() / $paginator->perPage()),
                'current_page' => $paginator->currentPage(),
                'limit'        => $paginator->perPage(),
            ],
            $this->resourceKeyPlural => $paginator->items(),
        ]);
    }

    /**
     * Respond with a given response.
     *
     * @param mixed $data
     * @param array $headers
     *
     * @return Res
     */
    protected function respond($data, $code = null, $headers = [])
    {
        return response()->json(
            $data,
            $code ? $code : $this->getStatusCode(),
            $headers
        );
    }

    /**
     * Response with the current error.
     *
     * @param string $message
     *
     * @return Res
     */
    protected function respondWithError($message)
    {
        return $this->respond([
            'status'      => 'error',
            'status_code' =>  $this->statusCode,
            'message'     => $message,
        ]);
	}

	    /**
     * Generate a Response with a 403 HTTP header and a given message.
     *
     * @param $message
     *
     * @return Res
     */
    protected function errorForbidden($message = 'Forbidden')
    {
        return $this->setStatusCode(403)->respondWithError($message);
    }

    /**
     * Generate a Response with a 500 HTTP header and a given message.
     *
     * @param string $message
     *
     * @return Res
     */
    protected function errorInternalError($message = 'Internal Error')
    {
        return $this->setStatusCode(500)->respondWithError($message);
    }

    /**
     * Generate a Response with a 404 HTTP header and a given message.
     *
     * @param string $message
     *
     * @return Res
     */
    protected function errorNotFound($message = 'Resource Not Found')
    {
        return $this->setStatusCode(404)->respondWithError($message);
    }

    /**
     * Generate a Response with a 401 HTTP header and a given message.
     *
     * @param string $message
     *
     * @return Res
     */
    protected function errorUnauthorized($message = 'Unauthorized')
    {
        return $this->setStatusCode(401)->respondWithError($message);
    }

    /**
     * Generate a Response with a 400 HTTP header and a given message.
     *
     * @param string$message
     *
     * @return Res
     */
    protected function errorWrongArgs($message = 'Wrong Arguments')
    {
        return $this->setStatusCode(400)->respondWithError($message);
    }

    /**
     * Generate a Response with a 501 HTTP header and a given message.
     *
     * @param string $message
     *
     * @return Res
     */
    protected function errorNotImplemented($message = 'Not implemented')
    {
        return $this->setStatusCode(501)->respondWithError($message);
    }
}