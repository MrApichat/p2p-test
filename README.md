Laravel version 9.45.1

<h1>Setting Project</h1>

 สร้าง table ให้ database รวมถึง seed data ด้วย command:
 
 <b>&emsp;php artisan migrate:fresh --seed</b>
 
<h1>How to run project</h1>
 
 service จะให้ port 8000 รันโดยใช้ command:

  <b>&emsp; php artisan serve</b>
  
  
  <h3>เส้น api ทั้งหมด </h3>
  
  POST /api/login</br>
  POST /api/register</br>
  GET /api/merchant_orders</br>
  
  <h5>require authorization bearer token</h5>
  
  GET /api/wallets</br>
  POST /api/transfer_order</br>
  GET /api/transfer_orders</br>
  POST /api/merchant_orders</br>
  POST /api/trade_orders/</br>
  PUT /api/trade_orders/{id}</br>
  DELETE /api/trade_orders/{id}</br>
  DELETE /api/merchant_orders/{id}</br>
  GET /api/trade_orders</br>
  
