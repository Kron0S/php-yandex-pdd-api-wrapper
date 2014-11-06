<?php

namespace YandexPdd\Api;

class User extends AbstractApi
{
    public function all()
    {
        return $this->get('user');
    }

    public function show($id)
    {
        return $this->get('user/'.urlencode($id), array(
			'id' => $id
		));
    }

    public function create($params = array())
    {
        return $this->post('user', $params);
    }

    public function update($id, array $params = array())
    {
        $params['id'] = $id;

        return $this->put('user/'.urlencode($id), $params);
    }

    public function remove($id)
    {
        return $this->delete('user/'.urlencode($id), array(
			'id' => $id
		));
    }
}
