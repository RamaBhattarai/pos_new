<?php


defined('BASEPATH') OR exit('No direct script access allowed');

class Events_model extends CI_Model
{


    /*Read the data from DB */
    public function getEvents($start, $end)
    {
        $e2=date('Y-m-d', strtotime($end. ' - 60 days'));
        $sql = "SELECT * FROM pos_events WHERE (pos_events.start BETWEEN ? AND ?) OR (pos_events.end > ? ) ORDER BY pos_events.start ASC";
        return $this->db->query($sql, array($start, $end,$e2))->result();

    }

    /*Create new events */

    public function addEvent($title, $start, $end, $description, $color)
    {

        $data = array(
            'title' => $title,
            'start' => $start,
            'end' => $end,

            'description' => $description,
            'color' => $color
        );

        if ($this->db->insert('pos_events', $data)) {
            return true;
        } else {
            return false;
        }
    }

    /*Update  event */

    public function updateEvent($id, $title, $description, $color)
    {

        $sql = "UPDATE pos_events SET title = ?, description = ?, color = ? WHERE id = ?";
        $this->db->query($sql, array($title, $description, $color, $id));
        return ($this->db->affected_rows() != 1) ? false : true;
    }


    /*Delete event */

    public function deleteEvent()
    {

        $sql = "DELETE FROM pos_events WHERE id = ?";
        $this->db->query($sql, array($_GET['id']));
        return ($this->db->affected_rows() != 1) ? false : true;
    }

    /*Update  event */

    public function dragUpdateEvent()
    {

        $sql = "UPDATE pos_events SET  pos_events.start = ? ,pos_events.end = ?  WHERE id = ?";
        $this->db->query($sql, array($_POST['start'], $_POST['end'], $_POST['id']));
        return ($this->db->affected_rows() != 1) ? false : true;


    }

}
