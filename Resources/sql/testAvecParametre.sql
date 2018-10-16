SELECT
*
FROM 
main
WHERE 1=1
AND ( 
    value = '{{value1}}'
OR 
    value = '{{value2}}'
)
--ORDER BY 
;