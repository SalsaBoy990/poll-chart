<?php

namespace AG\PollChart\Crud;

interface CrudInterface
{
    /**
     * Post actions switcher function, the most important method
     * that handles form operations
     */
    public function postAction(): void;

    /**
     * Get list of all members (wp menu page)
     * @return void
     */
    public function listTable(): void;

    /**
     * Add new member (wp menu page)
     * @return void
     */
    public function insertRecord(): void;

    /**
     * Insert new record, add new team member
     * @return void
     */
    public function handleInsert(): void;

    /**
     * Update current member
     * @return void
     */
    public function handleUpdate(): void;

    /**
     * delete current member
     * @return void
     */
    public function handleDelete(): void;
}
