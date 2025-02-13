from django.shortcuts import render, redirect
from django.contrib.auth import login, logout, authenticate
from django.contrib.auth.decorators import login_required
from .models import Customer
from repairs.models import *
from django.contrib import messages
from django.core.paginator import Paginator, EmptyPage, PageNotAnInteger
from django.db import IntegrityError
from django.http import JsonResponse
from django.views.decorators.csrf import csrf_exempt



# from repairs.models import Repair


# Dashboard View
@login_required
def dashboard(request):
    customer_count = Customer.objects.count()
    # Example of fetching additional data for the dashboard
    repair_count = Repair.objects.all().count()  # Replace with actual query
    active_repair_count = 5  # Replace with actual query
    # activity_logs = []  # Replace with actual logs query
    statuses = {}
    fetch_status = RepairStatus.objects.all()
    activities = ActivityLog.objects.order_by('-created_at')[:5]
    print("check", activities.values('description', 'created_at'))

    statuses["Total"] = repair_count
    for obj in fetch_status:
        statuses[obj.name] = Repair.objects.filter(status=obj).count()

    repair_statuses = RepairStatus.objects.all()
    print("statuses", statuses)
    locations = Sites.objects.prefetch_related('locations').all()

    context = {
        'customer_count': customer_count,
        'repair_count': repair_count,
        'active_repair_count': active_repair_count,
        'activity_logs': activities,
        'statuses': statuses,
        'repair_statuses': repair_statuses,
        "locations": locations
    }
    return render(request, 'dashboard.html', context)

# Login View
def login_view(request):
    if request.method == 'POST':
        username = request.POST.get('username', '').strip()
        password = request.POST.get('password', '').strip()

        # Validation: Check if fields are empty
        if not username or not password:
            messages.error(request, "Username and password are required.")
            return render(request, 'login.html')

        user = authenticate(request, username=username, password=password)

        if user:
            login(request, user)
            return redirect('dashboard')
        else:
            messages.error(request, "Invalid username or password.")
            return render(request, 'login.html')

    return render(request, 'login.html')

# Logout View
@login_required
def logout_view(request):
    logout(request)
    return redirect('login')

# Customers List View
@login_required
def customers_list(request):
    customers = Customer.objects.all()
    page = request.GET.get('page', 1)  # Get the current page number from the request
    paginator = Paginator(customers, 10)  # 10 logs per page

    try:
        customers = paginator.page(page)
    except PageNotAnInteger:
        customers = paginator.page(1)  # If page is not an integer, show the first page
    except EmptyPage:
        customers = paginator.page(paginator.num_pages)  # If page is out of range, show the last page
    return render(request, 'customers/customers_list.html', {'customers': customers, 'messages': None})

# Add Customer
@login_required
def add_customer(request):
    if request.method == 'POST':
        first_name = request.POST.get('first_name')
        last_name = request.POST.get('last_name')
        email = request.POST.get('email')
        phone = request.POST.get('phone')
        postcode = request.POST.get('postcode')

        try:
            customer = Customer.objects.create(
                first_name=first_name,
                last_name=last_name,
                email=email,
                phone=phone,
                postcode=postcode
            )
            ActivityLog.objects.create(
                description=f"Added new customer {customer.first_name} {customer.last_name}",
                user=request.user
            )
            return redirect('customers_list')
        except IntegrityError:
            # Handle duplicate phone number
            error_message = f"Exception in adding customer. The phone number {phone} is already associated with another customer."
            customers = Customer.objects.all()  # Fetch customers to render the list view
            return render(request, 'customers/customers_list.html', {
                'messages': error_message,
                'customers': customers,  # Pass customer data to display the list
            })
    return redirect('customers_list')

# Edit Customer
@login_required
@csrf_exempt
def edit_customer(request, pk):
    customer = Customer.objects.get(pk=pk)
    if request.method == 'POST':
        phone = request.POST.get('phone')
        try:
            customer.first_name = request.POST.get('first_name')
            customer.last_name = request.POST.get('last_name')
            customer.email = request.POST.get('email')
            customer.phone = request.POST.get('phone')
            customer.postcode = request.POST.get('postcode')
            customer.save()
            ActivityLog.objects.create(description=f"Edit customer {customer.first_name} {customer.last_name}", user=request.user)
            return JsonResponse({'success': True, "message": "Customer added successfully."}, status=200)
        except IntegrityError:
            # Handle duplicate phone number
            error_message = f"Exception in updating Customer. The phone number {phone} is already associated with another customer."
            # customers = Customer.objects.all()  # Fetch customers to render the list view
            return JsonResponse({'success': False, 'message': error_message}, status=400)

    return JsonResponse({'success': False, 'message': "Something went wrong."}, status=500)

# Delete Customer
@login_required
def delete_customer(request, pk):
    customer = Customer.objects.get(pk=pk)
    if request.method == 'POST':
        ActivityLog.objects.create(description=f"Delete customer {customer.first_name} {customer.last_name}", user=request.user)
        customer.delete()
        return redirect('customers_list')
    return redirect('customers_list')


@login_required
def profile_view(request):
    return render(request, 'profile.html', {'user': request.user})


@login_required
def edit_profile(request):
    if request.method == "POST":
        user = request.user
        user.first_name = request.POST.get('first_name', user.first_name)
        user.last_name = request.POST.get('last_name', user.last_name)
        user.email = request.POST.get('email', user.email)
        user.username = request.POST.get('username', user.username)
        user.save()
        messages.success(request, "Your profile has been updated successfully!")
        return redirect('profile')
    return redirect('profile')
