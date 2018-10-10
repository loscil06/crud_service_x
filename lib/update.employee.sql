UPDATE `service_x_employees`.`employees`
SET
`id` = :id,
`nombreCompleto` = :nombreCompleto,
`email` = :email,
`nss` = :nss,
`salary` = :salary,
`entry_date` = :entry_date,
`departments_id` = :departments_id,
`employee_states_id` = :employee_states_id
WHERE `id` = :expr;
