<style>
  /* Container & cards */
  .plan-card {
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    background: #fff;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    height: 100%;
  }
  .plan-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
  }

  /* Highlight the recommended plan */
  .plan-card.recommended {
    border: 3px solid #2a9d8f;
    background: linear-gradient(135deg, #d0f0ed, #a0d8d3);
  }

  /* Plan title & price */
  .plan-title {
    font-weight: 700;
    font-size: 1.75rem;
    margin-bottom: 0.25rem;
  }
  .plan-price {
    font-size: 1.25rem;
    color: #2a9d8f;
    margin-bottom: 1.25rem;
  }

  /* Feature list */
  .plan-features {
    flex-grow: 1;
    margin-bottom: 1.5rem;
    list-style: none;
    padding-left: 0;
  }
  .plan-features li {
    position: relative;
    padding-left: 28px;
    margin-bottom: 12px;
    font-size: 0.95rem;
    color: #555;
  }
  .plan-features li::before {
    content: "✓";
    position: absolute;
    left: 0;
    color: #2a9d8f;
    font-weight: bold;
  }

  /* Buttons */
  .subscribe-btn {
    border-radius: 50px;
    font-weight: 600;
    padding: 0.6rem 2rem;
    font-size: 1rem;
    transition: background-color 0.3s ease;
  }
  .subscribe-btn.basic {
    background-color: #264653;
    color: white;
  }
  .subscribe-btn.basic:hover {
    background-color: #1b2e3d;
  }
  .subscribe-btn.standard {
    background-color: #2a9d8f;
    color: white;
  }
  .subscribe-btn.standard:hover {
    background-color: #1f6f6a;
  }
  .subscribe-btn.premium {
    background-color: #e76f51;
    color: white;
  }
  .subscribe-btn.premium:hover {
    background-color: #b44b33;
  }

</style>

<div class="content-body">
  <div class="card p-4 shadow-sm">
    <div class="text-center mb-5">
      <h2 class="fw-bold">Choose Your Subscription Plan</h2>
      <p class="text-muted fs-5">Pick the best plan that fits your business needs</p>
    </div>
    <div class="row g-4 justify-content-center">

      <!-- Basic Plan -->
      <div class="col-md-4">
        <div class="plan-card">
          <h3 class="plan-title text-primary text-center">Basic Plan</h3>
          <p class="plan-price text-center">Rs. 25,000 </p>
          <ul class="plan-features">
            <li>3 Warehouses Login</li>
            <li>800 Product Limit</li>
            <li>Extra Product Charges: Rs 1500/100</li>
            <li>Extra Product Warehouse Charges: 3500 each</li>
            <li>3 Staff Logins</li>
            <li>Billing and Inventory</li>
            <li>Low Stock Alert</li>
            <li>Expiry Date Tracking</li>
            <li>Stock Worth Calculation</li>
            <li>7 Days Top Trending Product</li>
            <li>Stock Transfer</li>
            <li>Purchase and Supplier Management</li>
            <li>Reporting: Sales/Purchase, Item-wise, Daily, Monthly and Custom Report Builder</li>
            <li>Expense Management</li>
            <li>Accounting Module</li>
            <li>User Roles and Permissions</li>
            <li>Basic Dashboard</li>
            <li>Basic Training</li>
          </ul>
          <!-- <button class="subscribe-btn basic w-100">Subscribe</button> -->
        </div>
      </div>

      <!--  Standard Plan - Recommended -->
      <div class="col-md-4">
        <div class="plan-card recommended">
          <h3 class="plan-title text-success text-center">Standard Plan</h3>
          <p class="plan-price text-success text-center">Rs. 40,000 </p>
          <ul class="plan-features">
            <li>6 Warehouses</li>
            <li>2000 Product Limit</li>
            <li>5 Staff Logins</li>
            <li>Billing and Inventory</li>
            <li>Low Stock Alert</li>
            <li>Expiry Date Tracking</li>
            <li>Stock Worth Calculation</li>
            <li>1 Month Top Trending Product</li>
            <li>Stock Transfer</li>
            <li>Purchase and Supplier Management</li>
            <li>Reporting</li>
            <li>Expense Management</li>
            <li>Accounting Module</li>
            <li>User Roles and Permissions</li>
            <li>Basic Dashboard</li>
            <li>3 Month Priority Training</li>
          </ul>
          <!-- <button class="subscribe-btn standard w-100">Subscribe</button> -->
        </div>
      </div>

      <!-- Premium Plan -->
      <div class="col-md-4">
        <div class="plan-card">
          <h3 class="plan-title text-warning text-center">Premium Plan</h3>
          <p class="plan-price text-warning text-center">Rs. 60,000 </p>
          <ul class="plan-features">
            <li>10 Warehouses</li>
            <li>5000 Product Limit</li>
            <li>Unlimited Staff Logins</li>
            <li>Billing and Inventory</li>
            <li>Low Stock Alert</li>
            <li>Expiry Date Tracking</li>
            <li>Stock Worth Calculation</li>
            <li>Custom Days Top Trending Product</li>
            <li>Stock Transfer</li>
            <li>Purchase and Supplier Management</li>
            <li>Reporting</li>
            <li>Expense Management</li>
            <li>Accounting Module</li>
            <li>User Roles and Permissions</li>
            <li>Real-time Dashboard</li>
            <li>3 Month Priority Training</li>
            <li>1 Business Location Creation</li>
          </ul>
          <!-- <button class="subscribe-btn premium w-100">Subscribe</button> -->
        </div>
      </div>

    </div>
  </div> 
</div>
