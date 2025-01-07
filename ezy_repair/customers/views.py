from django.shortcuts import render
from .models import Customer

def customers_list(request):
    customers = Customer.objects.all()
    return render(request, 'customers/customers_list.html', {'customers': customers})
