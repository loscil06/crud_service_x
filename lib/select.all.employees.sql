SELECT employees.nombreCompleto, employees.email, employees.entry_date, employees.nss, employees.salary, departments.department, employee_states.state
FROM employees INNER JOIN departments ON employees.departments_id = departments.id
INNER JOIN employee_states ON employees.employee_states_id = employee_states.id