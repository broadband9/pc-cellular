from django.db import models

class RepairStatus(models.Model):
    name = models.CharField(max_length=50)

    def __str__(self):
        return self.name


class Location(models.Model):
    name = models.CharField(max_length=100)

    def __str__(self):
        return self.name


class Make(models.Model):
    name = models.CharField(max_length=100)

    def __str__(self):
        return self.name


class ActivityLog(models.Model):
    description = models.TextField()
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"{self.description} - {self.created_at}"


class Repair(models.Model):
    customer = models.ForeignKey('customers.Customer', on_delete=models.CASCADE)
    device_type = models.CharField(max_length=50)
    status = models.ForeignKey(RepairStatus, on_delete=models.SET_NULL, null=True)
    location = models.ForeignKey(Location, on_delete=models.SET_NULL, null=True)
    make = models.ForeignKey(Make, on_delete=models.SET_NULL, null=True)
    model = models.CharField(max_length=100)
    issue_description = models.TextField()
    estimated_cost = models.DecimalField(max_digits=10, decimal_places=2)
    finalized_price = models.DecimalField(max_digits=10, decimal_places=2, null=True, blank=True)
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"Repair #{self.pk} - {self.device_type}"
