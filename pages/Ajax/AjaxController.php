<?php
namespace CustoDesk\Page\Ajax;

use CustoDesk\Page\Common\AlertType;
use CustoDesk\RateLimit;
use CustoDesk\RequestMetadata;

class AjaxController
{
    protected object $data;

    protected function addAlert(AlertType $type, string $text, bool $raw = false): void
    {
        if (!$raw)
            $text = htmlspecialchars($text);
        if (!isset($this->data->alerts))
            $this->data->alerts = [];
        $this->data->alerts[] = (object)[
            "type" => $type->value,
            "text" => $text
        ];
    }

    public function post(RequestMetadata $request): void
    {
        $this->data = (object)[];
        if (RateLimit::check())
        {
            $succeeded = false;
            $data = json_decode(file_get_contents("php://input"));
            if (is_object($data))
            {
                $succeeded = $this->onPost($request, $data);
            }

            if (!$succeeded)
            {
                if (!isset($this->data->alerts))
                {
                    $this->addAlert(AlertType::ERROR, "An error occurred.");
                }
                if (http_response_code() == 200)
                {
                    http_response_code(400);
                }
            }
        }
        else
        {
            $this->addAlert(AlertType::ERROR, "You're doing too many actions in a short time. Please wait, and try again.");
        }

        header("Content-Type: application/json");
        echo json_encode($this->data);
    }

    public function onPost(RequestMetadata $request, object $data): bool
    {
        return false;
    }
}