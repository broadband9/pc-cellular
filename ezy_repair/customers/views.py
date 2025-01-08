from django.shortcuts import render, redirect
from django.contrib.auth import login, logout, authenticate
from django.contrib.auth.decorators import login_required
from .models import Customer

# Dashboard View
@login_required
def dashboard(request):
    customer_count = Customer.objects.count()
    # Example of fetching additional data for the dashboard
    repair_count = 10  # Replace with actual query
    active_repair_count = 5  # Replace with actual query
    activity_logs = []  # Replace with actual logs query

    context = {
        'customer_count': customer_count,
        'repair_count': repair_count,
        'active_repair_count': active_repair_count,
        'activity_logs': activity_logs,
    }
    return render(request, 'dashboard.html', context)

# Login View
def login_view(request):
    if request.method == 'POST':
        username = request.POST.get('username')
        password = request.POST.get('password')
        user = authenticate(request, username=username, password=password)
        if user:
            login(request, user)
            return redirect('dashboard')
        else:
            return render(request, 'login.html', {'error': 'Invalid credentials'})
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
    return render(request, 'customers/customers_list.html', {'customers': customers})

# Add Customer
@login_required
def add_customer(request):
    if request.method == 'POST':
        name = request.POST.get('name')
        email = request.POST.get('email')
        phone = request.POST.get('phone')
        Customer.objects.create(name=name, email=email, phone=phone)
        return redirect('customers_list')
    return redirect('customers_list')

# Edit Customer
@login_required
def edit_customer(request, pk):
    customer = Customer.objects.get(pk=pk)
    if request.method == 'POST':
        customer.name = request.POST.get('name')
        customer.email = request.POST.get('email')
        customer.phone = request.POST.get('phone')
        customer.save()
        return redirect('customers_list')
    return redirect('customers_list')

# Delete Customer
@login_required
def delete_customer(request, pk):
    customer = Customer.objects.get(pk=pk)
    if request.method == 'POST':
        customer.delete()
        return redirect('customers_list')
    return redirect('customers_list')
