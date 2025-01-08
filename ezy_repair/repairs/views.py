from django.shortcuts import render, redirect, get_object_or_404
from django.contrib.auth.decorators import login_required
from .models import Repair, Location, Make, RepairStatus, ActivityLog
from customers.models import Customer


# Repairs
@login_required
def repairs_list(request):
    repairs = Repair.objects.all()
    statuses = RepairStatus.objects.all()
    customers = Customer.objects.all()
    locations = Location.objects.all()
    makes = Make.objects.all()
    return render(request, 'repairs/repairs_list.html', {'repairs': repairs, "statuses": statuses, "customers": customers, "locations": locations, "makes": makes})

@login_required
def add_repair(request):
    if request.method == "POST":
        # Handle repair creation logic
        customer_id = request.POST.get("customer")
        device_type = request.POST.get("device_type")
        status_id = request.POST.get("status")
        location_id = request.POST.get("location")
        make_id = request.POST.get("make")
        model = request.POST.get("model")
        issue_description = request.POST.get("issue_description")
        estimated_cost = request.POST.get("estimated_cost")
        finalized_price = request.POST.get("finalized_price")

        customer = get_object_or_404(Customer, id=customer_id)
        status = get_object_or_404(RepairStatus, id=status_id) if status_id else None
        location = get_object_or_404(Location, id=location_id) if location_id else None
        make = get_object_or_404(Make, id=make_id) if make_id else None

        Repair.objects.create(
            customer=customer,
            device_type=device_type,
            status=status,
            location=location,
            make=make,
            model=model,
            issue_description=issue_description,
            estimated_cost=estimated_cost,
            finalized_price=finalized_price,
        )

    return redirect('repairs_list')


@login_required
def add_location(request):
    if request.method == 'POST':
        name = request.POST.get('name')
        address = request.POST.get('address')
        if name and address:
            Location.objects.create(name=name, address=address)



    return redirect('locations')


@login_required
def add_make(request):
    if request.method == "POST":
        name = request.POST.get('name')
        address = request.POST.get('address')
        if name:
            Make.objects.create(name=name)
    return redirect('makes')


@login_required
def add_repair_status(request):
    if request.method == "POST":
        # Handle repair creation logic
        status = request.POST.get("name")
        description = request.POST.get("description")
        create_status = RepairStatus.objects.create(name=status, description=description)
        return redirect('repair_statuses')
    return redirect('repair_statuses')


@login_required
def edit_repair_status(request, id):
    status = get_object_or_404(RepairStatus, id=id)
    if request.method == "POST":
        # Update logic here
        status.name = request.POST.get("name")
        status.description = request.POST.get("description")
        status.save()
        return redirect('repair_statuses')  # Replace with the correct redirect
    return render(request, 'repairs/edit_repair_status.html', {'status': status})

@login_required
def edit_location(request, pk):
    status = get_object_or_404(Location, id=pk)
    if request.method == "POST":
        # Update logic here
        status.name = request.POST.get("name")
        status.address = request.POST.get("address")

        status.save()
        # return redirect('repairs_list')  # Replace with the correct redirect
    return redirect('locations')

@login_required
def edit_repair_delete(request, id):
    status = get_object_or_404(RepairStatus, id=id)
    if request.method == "POST":
        # Update logic here
        status.delete()
        return redirect('repair_statuses')  # Replace with the correct redirect
    return render(request, 'repairs/edit_repair_status.html')

@login_required
def location_delete(request, pk):
    status = get_object_or_404(Location, id=pk)
    if request.method == "POST":
        # Update logic here
        status.delete()
        # return redirect('locations')  # Replace with the correct redirect
    return redirect('locations')


@login_required
def edit_repair(request, pk):
    repair = get_object_or_404(Repair, pk=pk)
    if request.method == "POST":
        customer_id = request.POST.get("customer")
        device_type = request.POST.get("device_type")
        status_id = request.POST.get("status")
        location_id = request.POST.get("location")
        make_id = request.POST.get("make")
        model = request.POST.get("model")
        issue_description = request.POST.get("issue_description")
        estimated_cost = request.POST.get("estimated_cost")
        finalized_price = request.POST.get("finalized_price")
        print("issue description", issue_description)
        customer = get_object_or_404(Customer, id=customer_id)
        status = get_object_or_404(RepairStatus, id=status_id) if status_id else None
        location = get_object_or_404(Location, id=location_id) if location_id else None
        make = get_object_or_404(Make, id=make_id) if make_id else None
        repair.customer = customer
        repair.status = status
        repair.location = location
        repair.make = make
        repair.model = model
        repair.device_type = device_type
        repair.issue_description = issue_description
        repair.estimated_cost = estimated_cost
        repair.finalized_price = finalized_price
        repair.save()
    return redirect('repairs_list')

@login_required
def delete_repair(request, pk):
    repair = get_object_or_404(Repair, pk=pk)
    repair.delete()
    return redirect('repairs_list')

# Locations
@login_required
def locations(request):
    locations = Location.objects.all()
    return render(request, 'repairs/locations.html', {'locations': locations})

# Makes
@login_required
def makes(request):
    makes = Make.objects.all()
    return render(request, 'repairs/makes.html', {'makes': makes})


@login_required
def edit_make(request, pk):
    status = get_object_or_404(Make, id=pk)
    if request.method == "POST":
        # Update logic here
        status.name = request.POST.get("name")

        status.save()
        # return redirect('repairs_list')  # Replace with the correct redirect
    return redirect('makes')

@login_required
def delete_make(request, pk):
    repair = get_object_or_404(Make, pk=pk)
    repair.delete()
    return redirect('makes')


# Repair Statuses
@login_required
def repair_statuses(request):
    statuses = RepairStatus.objects.all()
    return render(request, 'repairs/repair_statuses.html', {'statuses': statuses})

# Activity Logs
@login_required
def activity_logs(request):
    logs = ActivityLog.objects.all()
    return render(request, 'repairs/activity_logs.html', {'logs': logs})
