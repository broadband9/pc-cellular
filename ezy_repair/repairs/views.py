from django.shortcuts import render
from .models import Repair, RepairStatus, Location, Make, ActivityLog
# from ..customers.models import Customer


def dashboard(request):
    context = {
        'repair_count': Repair.objects.count(),
        # 'customer_count': Customer.objects.count(),
        'activity_logs': ActivityLog.objects.order_by('-created_at')[:5],
    }
    return render(request, 'dashboard.html', context)


def repairs_list(request):
    repairs = Repair.objects.all()
    return render(request, 'repairs/repairs_list.html', {'repairs': repairs})


def repair_statuses(request):
    statuses = RepairStatus.objects.all()
    return render(request, 'repairs/repair_statuses.html', {'statuses': statuses})


def locations(request):
    locations = Location.objects.all()
    return render(request, 'repairs/locations.html', {'locations': locations})


def makes(request):
    makes = Make.objects.all()
    return render(request, 'repairs/makes.html', {'makes': makes})


def activity_logs(request):
    logs = ActivityLog.objects.order_by('-created_at')
    return render(request, 'repairs/activity_logs.html', {'logs': logs})
