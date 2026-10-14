<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Staff_model');
    }

    public function index()
    {
        $keyword = $this->input->get('q');
        $keyword = is_string($keyword) ? trim($keyword) : '';

        $data['title']    = ($keyword !== '' ? 'Pencarian: ' . $keyword : 'Pencarian Staf') . ' - UNSRAT';
        $data['keyword']  = $keyword;
        $data['searched'] = strlen($keyword) >= 2;
        $data['results']  = $data['searched'] ? $this->Staff_model->search($keyword) : [];

        $this->load->view('templates/header', $data);
        $this->load->view('search/index', $data);
        $this->load->view('templates/footer');
    }

    public function suggest()
    {
        $keyword = trim($this->input->get('q') ?: '');
        if (strlen($keyword) < 2) {
            $this->output->set_content_type('application/json');
            echo json_encode([]);
            return;
        }

        $results = $this->Staff_model->search($keyword);
        $out = [];
        foreach (array_slice($results, 0, 8) as $s) {
            $out[] = [
                'name'            => $s->full_title_name ?: $s->name,
                'slug'            => $s->slug,
                'department_name' => $s->department_name,
            ];
        }

        $this->output->set_content_type('application/json');
        echo json_encode($out);
    }
}
