Laravel version 9.45.1

<h1>Setting Project</h1>

 สร้าง table ให้ database รวมถึง seed data ด้วย command:
 
 <b>&emsp;php artisan migrate:fresh --seed</b>
 
<h1>How to run project</h1>
 
 service จะให้ port 8000 รันโดยใช้ command:

  <b>&emsp; php artisan serve</b>
  
  
  <h3>เส้น api ทั้งหมด </h3>
  
  POST /api/login
  POST /api/register
  GET /api/merchant_orders
  
  <h5>require authorization bearer token</h5>
  
  GET /api/wallets
  POST /api/transfer_order
  GET /api/transfer_orders
  POST /api/merchant_orders
  POST /api/trade_orders/
  PUT /api/trade_orders/{id}
  DELETE /api/trade_orders/{id}
  DELETE /api/merchant_orders/{id}
  GET /api/trade_orders
  
