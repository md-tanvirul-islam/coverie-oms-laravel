<div class="container py-3">
    {{-- 4 Cards --}}
    <div class="row g-3 mb-4">
        <!-- Confirmed Orders -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body d-flex align-items-center">

                    <div class="icon-box rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                        <i class="bi bi-bag-check-fill text-primary fs-3"></i>
                    </div>

                    <div>
                        <h6 class="text-muted mb-1">Confirmed Orders Quantity</h6>
                        <h3 class="mb-0">{{ number_format($confirmedQty) }}</h3>
                    </div>

                </div>
            </div>
        </div>

        <!-- Return Quantity -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body d-flex align-items-center">

                    <div class="icon-box rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                        <i class="bi bi-arrow-counterclockwise text-danger fs-3"></i>
                    </div>

                    <div>
                        <h6 class="text-muted mb-1">Return Quantity (Paid Invoice)</h6>
                        <h3 class="mb-0">{{ number_format($returnQty) }}</h3>
                    </div>

                </div>
            </div>
        </div>

        <!-- Delivery Quantity -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body d-flex align-items-center">

                    <div class="icon-box rounded-circle bg-success bg-opacity-10 p-3 me-3">
                        <i class="bi bi-truck text-success fs-3"></i>
                    </div>

                    <div>
                        <h6 class="text-muted mb-1">Delivery Quantity (Paid Invoice)</h6>
                        <h3 class="mb-0">{{ number_format($deliveryQty) }}</h3>
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>
